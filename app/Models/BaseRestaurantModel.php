<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Model dasar multi-tenant per restoran.
 *
 * Otomatis memfilter query berdasarkan restaurant_id di session,
 * sehingga admin/kasir/dapur hanya melihat data restoran mereka.
 */
class BaseRestaurantModel extends Model
{
    /** @var int|null ID restoran aktif dari session login */
    protected $restaurantId = null;

    public function __construct()
    {
        parent::__construct();
        $this->setCurrentRestaurant();
    }

    /**
     * Set restoran aktif — dari parameter atau dari session.
     */
    public function setCurrentRestaurant($restaurantId = null)
    {
        if ($restaurantId) {
            $this->restaurantId = $restaurantId;
        } else {
            $session = session();
            $this->restaurantId = $session->get('restaurant_id');
        }
        return $this;
    }

    public function getCurrentRestaurant()
    {
        return $this->restaurantId;
    }

    /**
     * Tambahkan WHERE restaurant_id jika kolom ada di allowedFields model anak.
     */
    protected function applyRestaurantFilter()
    {
        if ($this->restaurantId && in_array('restaurant_id', $this->allowedFields ?? [])) {
            $this->where('restaurant_id', $this->restaurantId);
        }
        return $this;
    }

    /**
     * Cari satu record; dibatasi ke restoran aktif bila memungkinkan.
     */
    public function find($id = null)
    {
        if (empty($id)) {
            return null;
        }

        if ($this->restaurantId && in_array('restaurant_id', $this->allowedFields ?? [])) {
            return $this->where('restaurant_id', $this->restaurantId)
                        ->where('id', $id)
                        ->first();
        }

        return parent::find($id);
    }

    public function findAll($limit = 0, $offset = 0)
    {
        $this->applyRestaurantFilter();
        return parent::findAll($limit, $offset);
    }

    /**
     * Saat insert, otomatis isi restaurant_id agar data masuk ke tenant yang benar.
     */
    public function insert($data = null, bool $returnID = true)
    {
        if ($this->restaurantId && in_array('restaurant_id', $this->allowedFields ?? [])) {
            if (is_array($data)) {
                $data['restaurant_id'] = $this->restaurantId;
            }
        }

        return parent::insert($data, $returnID);
    }

    public function update($id = null, $row = null): bool
    {
        if ($this->restaurantId && in_array('restaurant_id', $this->allowedFields ?? [])) {
            $this->where('restaurant_id', $this->restaurantId);
        }

        return parent::update($id, $row);
    }

    public function delete($id = null, bool $purge = false)
    {
        if ($this->restaurantId && in_array('restaurant_id', $this->allowedFields ?? [])) {
            $this->where('restaurant_id', $this->restaurantId);
        }

        return parent::delete($id, $purge);
    }

    public function countAll($reset = true)
    {
        if ($this->restaurantId && in_array('restaurant_id', $this->allowedFields ?? [])) {
            $this->where('restaurant_id', $this->restaurantId);
        }

        return parent::countAll($reset);
    }

    public function first()
    {
        if ($this->restaurantId && in_array('restaurant_id', $this->allowedFields ?? [])) {
            $this->where('restaurant_id', $this->restaurantId);
        }

        return parent::first();
    }

    public function paginate($perPage = 20, $group = 'default', $page = null, $onlyCount = false)
    {
        if ($this->restaurantId && in_array('restaurant_id', $this->allowedFields ?? [])) {
            $this->where('restaurant_id', $this->restaurantId);
        }

        return parent::paginate($perPage, $group, $page, $onlyCount);
    }
}
