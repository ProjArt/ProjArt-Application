<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use voku\helper\HtmlDomParser;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    const ROLE_STUDENT = 'student';
    const ROLE_TEACHER = 'teacher';
    const ROLE_ADMIN = 'admin';
    const ROLES = [User::ROLE_STUDENT, User::ROLE_TEACHER, User::ROLE_ADMIN];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'card_money',
        'gaps_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'theme_id',
        'password',
        'remember_token',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $with = [
        'theme'
    ];

    protected function password(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => decrypt($value),
            set: fn ($value) => encrypt($value)
        );
    }

    public function setPersonalNumber()
    {
        if ($this->gaps_id == 0) {
            $content = file_get_contents("https://" . $this->username . ":" . urlencode($this->password) . "@gaps.heig-vd.ch/consultation/horaires/");
            $dom = HtmlDomParser::str_get_html($content);
            $element = $dom->findOne('div.scheduleLinks span.navLink a'); // "$element" === instance of "SimpleHtmlDomInterface"

            preg_match('/[0-9]{5}/', $element->href, $matches);
            $this->update([
                'gaps_id' => $matches[0],
            ]);
        }
    }

    public function setClass()
    {
        if (count($this->classes) == 0) {
            $this->classes()->firstOrCreate([
                'name' => 'M49-1',
            ]);
        }
    }

    public function getActualHoraireLink()
    {
        $url = "https://" . $this->username . ":" . urlencode($this->password) . "@gaps.heig-vd.ch/consultation/horaires/";
        $content = file_get_contents($url);
        $dom = HtmlDomParser::str_get_html($content);
        $links = $dom->findMulti("a");
        $found = "";
        foreach ($links as $link) {
            if (str_contains($link->innerText, "[Outlook et Apple Calendar]")) {
                $found = $link->href;
            }
        }
        return $url . $found;
    }

    public function theme()
    {
        return $this->belongsTo(Theme::class);
    }

    public function person()
    {
        if ($this->role == User::ROLE_STUDENT) {
            return $this->hasOne(Student::class);
        } else if ($this->role == User::ROLE_TEACHER) {
            return $this->hasOne(Teacher::class);
        }
    }

    public function calendarsFollow()
    {
        return $this->belongsToMany(Calendar::class, 'calendar_user_follow', 'user_id', 'calendar_id');
    }

    public function calendarsOwn()
    {
        return $this->belongsToMany(CalendarOwn::class, 'calendar_user_own', 'user_id', 'calendar_id')->orderBy('name');
    }

    public function classrooms()
    {
        return $this->belongsToMany(Classroom::class, 'classroom_user', 'user_id', 'classroom_name', 'id', 'name');
    }

    public function marks()
    {
        return $this->hasMany(Mark::class);
    }

    public function absences()
    {
        return $this->hasMany(Absence::class);
    }
}
