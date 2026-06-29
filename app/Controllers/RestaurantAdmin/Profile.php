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

        if ($this->request->getMethod() === 'post') {
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

            // Handle logo upload
            $logo = $this->request->getFile('logo');
            $logoPath = $restaurant['logo'] ?? null;
            if ($logo && $logo->isValid() && !$logo->hasMoved()) {
                $logoName = $logo->getRandomName();
                $logo->move(FCPATH . 'uploads/logos', $logoName);
                $logoPath = 'uploads/logos/' . $logoName;
            }

            $this->restaurantModel->update($restaurantId, [
                'name'          => $this->request->getPost('name'),
                'address'       => $this->request->getPost('address'),
                'whatsapp'      => $this->request->getPost('whatsapp'),
                'description'   => $this->request->getPost('description'),
                'opening_hours' => $this->request->getPost('opening_hours'),
                'logo'          => $logoPath,
            ]);

            // Handle QRIS
            $qrisImage = $this->request->getFile('qris_image');
            $qrisImagePath = $qris['qris_image'] ?? null;
            if ($qrisImage && $qrisImage->isValid() && !$qrisImage->hasMoved()) {
                $qrisName = $qrisImage->getRandomName();
                $qrisImage->move(FCPATH . 'uploads/qris', $qrisName);
                $qrisImagePath = 'uploads/qris/' . $qrisName;
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
