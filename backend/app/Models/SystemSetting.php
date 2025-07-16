<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * SystemSetting Model
 * 
 * Represents system configuration settings
 */
class SystemSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'description',
        'is_public',
        'updated_by',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    /**
     * Boot method to handle cache invalidation
     */
    protected static function boot()
    {
        parent::boot();
        
        static::saved(function ($setting) {
            Cache::forget("system_setting_{$setting->key}");
            Cache::forget("system_settings_group_{$setting->group}");
            Cache::forget('system_settings_public');
        });

        static::deleted(function ($setting) {
            Cache::forget("system_setting_{$setting->key}");
            Cache::forget("system_settings_group_{$setting->group}");
            Cache::forget('system_settings_public');
        });
    }

    /**
     * Get setting value with proper type casting
     */
    public function getCastedValue()
    {
        switch ($this->type) {
            case 'boolean':
                return filter_var($this->value, FILTER_VALIDATE_BOOLEAN);
            case 'integer':
                return (int) $this->value;
            case 'float':
                return (float) $this->value;
            case 'json':
                return json_decode($this->value, true);
            default:
                return $this->value;
        }
    }

    /**
     * Set setting value with proper type handling
     */
    public function setCastedValue($value): void
    {
        switch ($this->type) {
            case 'boolean':
                $this->value = $value ? '1' : '0';
                break;
            case 'json':
                $this->value = json_encode($value);
                break;
            default:
                $this->value = (string) $value;
        }
    }

    /**
     * Get a setting value by key
     */
    public static function get(string $key, $default = null)
    {
        return Cache::remember("system_setting_{$key}", 3600, function () use ($key, $default) {
            $setting = self::where('key', $key)->first();
            return $setting ? $setting->getCastedValue() : $default;
        });
    }

    /**
     * Set a setting value by key
     */
    public static function set(string $key, $value, string $type = 'string', string $group = 'general', string $updatedBy = null): self
    {
        $setting = self::updateOrCreate(
            ['key' => $key],
            [
                'type' => $type,
                'group' => $group,
                'updated_by' => $updatedBy,
            ]
        );
        
        $setting->setCastedValue($value);
        $setting->save();
        
        return $setting;
    }

    /**
     * Get all settings in a group
     */
    public static function getGroup(string $group): array
    {
        return Cache::remember("system_settings_group_{$group}", 3600, function () use ($group) {
            return self::where('group', $group)
                      ->get()
                      ->mapWithKeys(function ($setting) {
                          return [$setting->key => $setting->getCastedValue()];
                      })
                      ->toArray();
        });
    }

    /**
     * Get all public settings
     */
    public static function getPublicSettings(): array
    {
        return Cache::remember('system_settings_public', 3600, function () {
            return self::where('is_public', true)
                      ->get()
                      ->mapWithKeys(function ($setting) {
                          return [$setting->key => $setting->getCastedValue()];
                      })
                      ->toArray();
        });
    }

    /**
     * Get loan configuration settings
     */
    public static function getLoanConfig(): array
    {
        return [
            'interest_rates' => [
                'base_rate' => self::get('loan_base_interest_rate', 2.0),
                'promotional_rate' => self::get('loan_promotional_rate', 1.5),
                'penalty_rate' => self::get('loan_penalty_rate', 3.0),
            ],
            'loan_limits' => [
                'min_amount' => self::get('loan_min_amount', 10000000),
                'max_amount' => self::get('loan_max_amount', 500000000),
                'step_amount' => self::get('loan_step_amount', 1000000),
                'default_amount' => self::get('loan_default_amount', 50000000),
            ],
            'loan_terms' => self::get('loan_terms', [6, 12, 18, 24, 36, 48]),
            'quick_amounts' => self::get('loan_quick_amounts', [50000000, 100000000, 200000000]),
        ];
    }

    /**
     * Initialize default system settings
     */
    public static function initializeDefaults(): void
    {
        $defaults = [
            // Loan settings
            ['key' => 'loan_base_interest_rate', 'value' => '2.0', 'type' => 'float', 'group' => 'loan', 'description' => 'Base interest rate (%)', 'is_public' => true],
            ['key' => 'loan_promotional_rate', 'value' => '1.5', 'type' => 'float', 'group' => 'loan', 'description' => 'Promotional interest rate (%)', 'is_public' => true],
            ['key' => 'loan_penalty_rate', 'value' => '3.0', 'type' => 'float', 'group' => 'loan', 'description' => 'Penalty interest rate (%)', 'is_public' => true],
            ['key' => 'loan_min_amount', 'value' => '10000000', 'type' => 'integer', 'group' => 'loan', 'description' => 'Minimum loan amount', 'is_public' => true],
            ['key' => 'loan_max_amount', 'value' => '500000000', 'type' => 'integer', 'group' => 'loan', 'description' => 'Maximum loan amount', 'is_public' => true],
            ['key' => 'loan_step_amount', 'value' => '1000000', 'type' => 'integer', 'group' => 'loan', 'description' => 'Loan amount step', 'is_public' => true],
            ['key' => 'loan_default_amount', 'value' => '50000000', 'type' => 'integer', 'group' => 'loan', 'description' => 'Default loan amount', 'is_public' => true],
            ['key' => 'loan_terms', 'value' => '[6,12,18,24,36,48]', 'type' => 'json', 'group' => 'loan', 'description' => 'Available loan terms (months)', 'is_public' => true],
            ['key' => 'loan_quick_amounts', 'value' => '[50000000,100000000,200000000]', 'type' => 'json', 'group' => 'loan', 'description' => 'Quick loan amounts', 'is_public' => true],
            
            // File upload settings
            ['key' => 'max_file_size', 'value' => '10240', 'type' => 'integer', 'group' => 'upload', 'description' => 'Maximum file size (KB)', 'is_public' => false],
            ['key' => 'allowed_file_types', 'value' => '["jpg","jpeg","png","pdf"]', 'type' => 'json', 'group' => 'upload', 'description' => 'Allowed file types', 'is_public' => false],
            
            // Rate limiting settings
            ['key' => 'rate_limit_general', 'value' => '100', 'type' => 'integer', 'group' => 'rate_limit', 'description' => 'General API rate limit per minute', 'is_public' => false],
            ['key' => 'rate_limit_auth', 'value' => '10', 'type' => 'integer', 'group' => 'rate_limit', 'description' => 'Auth API rate limit per minute', 'is_public' => false],
            ['key' => 'rate_limit_upload', 'value' => '20', 'type' => 'integer', 'group' => 'rate_limit', 'description' => 'Upload API rate limit per minute', 'is_public' => false],
            ['key' => 'rate_limit_admin', 'value' => '200', 'type' => 'integer', 'group' => 'rate_limit', 'description' => 'Admin API rate limit per minute', 'is_public' => false],
            
            // System settings
            ['key' => 'app_name', 'value' => 'Loan System', 'type' => 'string', 'group' => 'general', 'description' => 'Application name', 'is_public' => true],
            ['key' => 'app_version', 'value' => '1.0.0', 'type' => 'string', 'group' => 'general', 'description' => 'Application version', 'is_public' => true],
            ['key' => 'maintenance_mode', 'value' => '0', 'type' => 'boolean', 'group' => 'general', 'description' => 'Maintenance mode', 'is_public' => true],
        ];

        foreach ($defaults as $setting) {
            self::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }

    /**
     * Scope for public settings
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope for specific group
     */
    public function scopeInGroup($query, string $group)
    {
        return $query->where('group', $group);
    }
}