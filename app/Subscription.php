<?php

namespace app;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = [
        'email',
        'newsletter_id',
        'confirmed_at',
        'confirmation_id'
    ];

    /**
     * A Subscription has one Newsletter
     *
     * @return object
     */
    public function newsletter()
    {
        return $this->hasOne('app\Newsletter');
    }
}