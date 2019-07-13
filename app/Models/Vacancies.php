<?php


namespace App\Models;


class Vacancies extends Base
{
    protected $table = 'vacancies';

    protected $fillable = [
        'roleId',
        'teamId'
    ];

    protected $hidden = ['created_at', 'updated_at'];

    public function team()
    {
        return $this->belongsTo(Team::class, 'teamId');
    }

    public function role()
    {
        return $this->belongsTo(Roles::class);
    }
}