<?php

namespace App\Models;

use App\Notifications\OneSignal;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Http;
use Laravel\Sanctum\HasApiTokens;
use voku\helper\HtmlDomParser;
use \Staudenmeir\EloquentHasManyDeep\HasRelationships;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRelationships;


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
        'gaps_id',
        'onesignal_id',
        "theme_id"
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
        'theme',
    ];

    protected $appends = [
        'gaps_user'
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
            $response = Http::withBasicAuth($this->username, $this->password)
                                ->get("https://gaps.heig-vd.ch/consultation/horaires/");
            $dom = HtmlDomParser::str_get_html($response->body());
            $element = $dom->findOne('div.scheduleLinks span.navLink a'); 

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
        $url = "https://gaps.heig-vd.ch/consultation/horaires/";
        $response = Http::withBasicAuth($this->username, $this->password)->get($url);
        $dom = HtmlDomParser::str_get_html($response->body());
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

    public function calendars()
    {
        return $this->belongsToMany(Calendar::class)->withPivot(['rights', 'color'])->orderBy('name');
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

    public function events()
    {
        return $this->hasManyDeep(Event::class, ['calendar_user', Calendar::class]);
    }

    public function channels()
    {
        return $this->belongsToMany(Channel::class);
    }

    public function notifications()
    {
        return $this->hasManyDeep(Notification::class, ['channel_user', Channel::class])->orderBy('created_at', 'desc');
    }

    public function getGapsUserAttribute()
    {
        return GapsUser::whereUsername($this->username)->first();
    }

    public function getIsShareableAttribute()
    {
        return $this->id != auth('sanctum')->user()->id;
    }

    public function markmodules()
    {
        return $this->hasMany(MarkModule::class);
    }
}
