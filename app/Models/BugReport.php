<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BugReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'uuid',
        'vulnerability_type',
        'title',
        'description'
    ];

    /**
     * Get user of this model
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Find bug reports of a user
     *
     * @param integer $userId
     * 
     * @return array
     */
    public function findByUserId(int $userId)
    {
        $reports = $this->where('user_id', $userId)->get();

        $data = [];
        foreach($reports as $report){
            $data[] = [
                'uuid' => $report->uuid,
                'vulnerability_type' => $report->vulnerability_type,
                'title' => $report->title,
                'description' => $report->description,
            ];
        }

        return $data;
    }
}
