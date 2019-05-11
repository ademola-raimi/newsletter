<?php

namespace app;

use Illuminate\Database\Eloquent\Model;

class Newsletter extends Model
{
    protected $fillable = [
        'title',
        'description',
        'user_id'
    ];

    /**
     * A Newsletter belongs to User
     *
     * @return object
     */
    public function user()
    {
        return $this->belongsTo('app\User');
    }

    /**
     * A Newsletter belongs to User
     *
     * @return object
     */
    public function subscription()
    {
        return $this->belongsTo('app\Subscription');
    }
}