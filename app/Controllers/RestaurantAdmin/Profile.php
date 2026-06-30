<?php

namespace App\Controllers\RestaurantAdmin;

use App\Controllers\BaseController;
use App\Models\Restaurant;
use App\Models\QRIS;

class Profile extends BaseController
{
    protected Restaurant $restaurantModel;
    protected QRIS $qrisModel;

    public function __construct()
    {
        $this->restaurantModel = new Restaurant();
        $this->qrisModel       = new QRIS();
    }

    public function edit()
    {
        $restaurantId = session('restaurant_id');
        $restaurant   = $this->restaurantModel->find($restaurantId);
        $qris         = $this->qrisModel->where('restaurant_id', $restaurantId)->first();

        if ($this->request->getMethod() === 'POST') {
            $rules = [
                'name'     => 'required|min_length[2]|max_length[255]',
                'whatsapp' => 'required|min_length[8]|max_length[20]',
            ];

            if (!$this->validate($rules)) {
                $content = view('resto/profile/edit', [
                    'restaurant' => $restaurant,
                    'qris'       => $qris,
                    'errors'     => $this->validator->getErrors(),
                ]);
                return view('layouts/Layout', ['title' => 'Profil Restoran', 'content' => $content]);
            }

            // ===== DEBUG SEMENTARA — HAPUS SETELAH MASALAH KETEMU =====
            log_message('error', '[DEBUG UPLOAD] Validasi form: LOLOS');
            log_message('error', '[DEBUG UPLOAD] $_FILES mentah: ' . print_r($_FILES, true));
            // ===== END DEBUG =====

            helper('upload');

            // Handle logo upload — pertahankan logo lama jika tidak upload baru
            $logo = $this->request->getFile('logo');

            // ===== DEBUG SEMENTARA =====
            log_message('error', '[DEBUG UPLOAD] $logo null? ' . ($logo === null ? 'YA' : 'TIDAK'));
            if ($logo !== null) {
                log_message('error', '[DEBUG UPLOAD] getError(): ' . $logo->getError());
                log_message('error', '[DEBUG UPLOAD] isValid(): ' . ($logo->isValid() ? 'TRUE' : 'FALSE'));
                log_message('error', '[DEBUG UPLOAD] getClientExtension(): ' . $logo->getClientExtension());
                log_message('error', '[DEBUG UPLOAD] getMimeType(): ' . $logo->getMimeType());
                log_message('error', '[DEBUG UPLOAD] getSize(): ' . $logo->getSize());
            }
            // ===== END DEBUG =====

            $logoPath = $restaurant['logo'] ?? null;
            try {
                $newLogoPath = move_validated_upload($logo, 'logos');
                // ===== DEBUG SEMENTARA =====
                log_message('error', '[DEBUG UPLOAD] newLogoPath hasil: ' . ($newLogoPath ?? 'NULL'));
                // ===== END DEBUG =====
                if ($newLogoPath !== null) {
                    $logoPath = $newLogoPath;
                }
            } catch (\RuntimeException $e) {
                // ===== DEBUG SEMENTARA =====
                log_message('error', '[DEBUG UPLOAD] EXCEPTION tertangkap: ' . $e->getMessage());
                // ===== END DEBUG =====
                $content = view('resto/profile/edit', [
                    'restaurant' => $restaurant,
                    'qris'       => $qris,
                    'errors'     => ['logo' => $e->getMessage()],
                ]);
                return view('layouts/Layout', ['title' => 'Profil Restoran', 'content' => $content]);
            }

            // ===== DEBUG SEMENTARA =====
            log_message('error', '[DEBUG UPLOAD] logoPath FINAL yang akan disimpan ke DB: ' . ($logoPath ?? 'NULL'));
            // ===== END DEBUG =====

            $this->restaurantModel->skipValidation(true)->update($restaurantId, [
                'name'          => $this->request->getPost('name'),
                'address'       => $this->request->getPost('address'),
                'whatsapp'      => $this->request->getPost('whatsapp'),
                'description'   => $this->request->getPost('description'),
                'opening_hours' => $this->request->getPost('opening_hours'),
                'logo'          => $logoPath,
            ]);

            // Handle QRIS — pertahankan gambar lama jika tidak upload baru
            $qrisImage = $this->request->getFile('qris_image');
            $qrisImagePath = $qris['qris_image'] ?? null;
            try {
                $newQrisPath = move_validated_upload($qrisImage, 'qris');
                if ($newQrisPath !== null) {
                    $qrisImagePath = $newQrisPath;
                }
            } catch (\RuntimeException $e) {
                $content = view('resto/profile/edit', [
                    'restaurant' => $restaurant,
                    'qris'       => $qris,
                    'errors'     => ['qris_image' => $e->getMessage()],
                ]);
                return view('layouts/Layout', ['title' => 'Profil Restoran', 'content' => $content]);
            }

            if ($this->request->getPost('merchant_name')) {
                $qrisData = [
                    'restaurant_id' => $restaurantId,
                    'merchant_name' => $this->request->getPost('merchant_name'),
                    'qris_image'    => $qrisImagePath,
                    'is_active'     => $this->request->getPost('qris_active') ? 1 : 0,
                ];
                if ($qris) {
                    $this->qrisModel->update($qris['id'], $qrisData);
                } else {
                    $this->qrisModel->insert($qrisData);
                }
            }

            return redirect()->to('/resto/profile')->with('success', 'Profil berhasil disimpan');
        }

        $content = view('resto/profile/edit', [
            'restaurant' => $restaurant,
            'qris'       => $qris,
        ]);
        return view('layouts/Layout', ['title' => 'Profil Restoran', 'content' => $content]);
    }
}