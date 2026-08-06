<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'escola_id',
        'papel',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function tarefas()
    {
        return $this->belongsToMany(Tarefa::class, 'tarefa_user');
    }

    public function escola()
    {
        return $this->belongsTo(Escola::class);
    }

    public function isMaster(): bool
    {
        return $this->papel === 'master';
    }

    public function isTarefas(): bool
    {
        return $this->papel === 'tarefas';
    }

    public function isGrade(): bool
    {
        return $this->papel === 'grade';
    }
}
