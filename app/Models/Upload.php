<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Upload extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'path',
        'original_name',
        'mimetype',
        'transcription',
        'user_id',
        'file_size',
        'duration',
        'language',
        'processing_status',
        'confidence_score',
        'metadata'
    ];

    protected $casts = [
        'file_size' => 'integer',
        'duration' => 'integer',
        'confidence_score' => 'float',
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the user that owns the upload
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get formatted file size
     */
    public function getFormattedFileSizeAttribute(): string
    {
        if (!$this->file_size) {
            return 'Unknown';
        }

        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Get formatted duration
     */
    public function getFormattedDurationAttribute(): string
    {
        if (!$this->duration) {
            return 'Unknown';
        }

        $seconds = $this->duration;
        $minutes = floor($seconds / 60);
        $remainingSeconds = $seconds % 60;
        
        if ($minutes > 0) {
            return sprintf('%d:%02d', $minutes, $remainingSeconds);
        }
        
        return $seconds . 's';
    }

    /**
     * Check if upload is audio file
     */
    public function isAudio(): bool
    {
        return str_starts_with($this->mimetype ?? '', 'audio/');
    }

    /**
     * Check if processing is completed
     */
    public function isProcessed(): bool
    {
        return $this->processing_status === 'completed';
    }

    /**
     * Get confidence level description
     */
    public function getConfidenceLevelAttribute(): string
    {
        if (!$this->confidence_score) {
            return 'Unknown';
        }

        if ($this->confidence_score >= 0.9) {
            return 'High';
        } elseif ($this->confidence_score >= 0.7) {
            return 'Medium';
        } elseif ($this->confidence_score >= 0.5) {
            return 'Low';
        }
        
        return 'Very Low';
    }

    /**
     * Scope for recent uploads
     */
    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for successful uploads
     */
    public function scopeSuccessful($query)
    {
        return $query->where('processing_status', 'completed');
    }

    /**
     * Scope for specific language
     */
    public function scopeLanguage($query, string $language)
    {
        return $query->where('language', $language);
    }
}
