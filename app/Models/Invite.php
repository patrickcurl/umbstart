<?php

declare(strict_types=1);
namespace App\Models;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invite extends Model
{
    use SoftDeletes;

    /**
     * @var array
     */
    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
    * @var array
    */
    protected $dates = ['claimed_at'];

    /**
     * @var  array
     */
    protected $casts = [
        'claimed_at'  => 'datetime'
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id', 'id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id', 'id');
    }

    public function inviteable()
    {
        return $this->morphTo();
    }

    public function getCode($isNew = false): string
    {
        if ($isNew === true) {
            $this->code = bin2hex(openssl_random_pseudo_bytes(16));
            $this->save();
        }

        return $this->code;
    }

    public static function findByCode($code) : self
    {
        return static::where('code', $code)->first();
    }
}
