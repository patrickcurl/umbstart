<?php

// @formatter:off
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * App\Models\Teamable
 *
 * @property string $team_id
 * @property string $teamable_type
 * @property int $teamable_id
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $teamable
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Teamable newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Teamable newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Teamable query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Teamable whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Teamable whereTeamableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Teamable whereTeamableType($value)
 */
	class Teamable extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\User
 *
 * @property int $id
 * @property string|null $first_name
 * @property string $last_name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $last_login
 * @property int|null $current_team_id
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Silber\Bouncer\Database\Ability[] $abilities
 * @property-read int|null $abilities_count
 * @property-read mixed $name
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Invite[] $invitesReceived
 * @property-read int|null $invites_received_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Invite[] $invitesSent
 * @property-read int|null $invites_sent_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Silber\Bouncer\Database\Role[] $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Team[] $sentInvites
 * @property-read int|null $sent_invites_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Team[] $teams
 * @property-read int|null $teams_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User filter($filters)
 * @method static bool|null forceDelete()
 * @method static \Sofa\Eloquence\Builder|\App\Models\User newModelQuery()
 * @method static \Sofa\Eloquence\Builder|\App\Models\User newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User orderByName()
 * @method static \Sofa\Eloquence\Builder|\App\Models\User query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereCurrentTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereIs($role)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereIsAll($role)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereIsNot($role)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereLastLogin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User withEmail($email)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User withName($name)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User withoutTrashed()
 */
	class User extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Media
 *
 * @property int $id
 * @property string $model_type
 * @property int $model_id
 * @property string $collection_name
 * @property string $name
 * @property string $file_name
 * @property string|null $mime_type
 * @property string $disk
 * @property int $size
 * @property mixed $manipulations
 * @property mixed $custom_properties
 * @property mixed $responsive_images
 * @property int|null $order_column
 * @property array $source
 * @property string|null $processed_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $extension
 * @property-read mixed $human_readable_size
 * @property-read mixed $type
 * @property-read \App\Models\Media $model
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Media newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Media newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Spatie\MediaLibrary\Models\Media ordered()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Media query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Media whereCollectionName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Media whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Media whereCustomProperties($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Media whereDisk($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Media whereFileName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Media whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Media whereManipulations($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Media whereMimeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Media whereModelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Media whereModelType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Media whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Media whereOrderColumn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Media whereProcessedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Media whereResponsiveImages($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Media whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Media whereSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Media whereUpdatedAt($value)
 */
	class Media extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Tag
 *
 * @property-write mixed $group
 * @method static \App\Traits\Cache\EloquentBuilder|\App\Models\Tag newModelQuery()
 * @method static \App\Traits\Cache\EloquentBuilder|\App\Models\Tag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Tag ordered($direction = 'asc')
 * @method static \App\Traits\Cache\EloquentBuilder|\App\Models\Tag query()
 */
	class Tag extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Basemodel
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Basemodel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Basemodel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Basemodel query()
 */
	class Basemodel extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\SearchableMessage
 *
 * @property int $id
 * @property int|null $uid
 * @property string|null $subject
 * @property array $from
 * @property array $to
 * @property array $cc
 * @property array $bcc
 * @property mixed $attachments
 * @property array $labels
 * @property string|null $text_body
 * @property string|null $html_body
 * @property string|null $processed_at
 * @property string|null $received_at
 * @property int $mailbox_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $searchable
 * @property-read mixed $from_emails
 * @property-read mixed $to_emails
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\MediaLibrary\Models\Media[] $images
 * @property-read int|null $images_count
 * @property-read \App\Models\Mailbox $mailbox
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Media[] $media
 * @property-read int|null $media_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SearchableMessage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SearchableMessage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SearchableMessage query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SearchableMessage whereAttachments($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SearchableMessage whereBcc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SearchableMessage whereCc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SearchableMessage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SearchableMessage whereFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SearchableMessage whereHtmlBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SearchableMessage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SearchableMessage whereLabels($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SearchableMessage whereMailboxId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SearchableMessage whereProcessedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SearchableMessage whereReceivedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SearchableMessage whereSearchable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SearchableMessage whereSubject($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SearchableMessage whereTextBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SearchableMessage whereTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SearchableMessage whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SearchableMessage whereUpdatedAt($value)
 */
	class SearchableMessage extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Mailbox
 *
 * @property int $id
 * @property string $username
 * @property string $password
 * @property string $host
 * @property int $port
 * @property string $encryption
 * @property bool $validate_cert
 * @property array|null $ignored_folders
 * @property int|null $user_id
 * @property bool $active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $config
 * @property-read mixed $message_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Message[] $messages
 * @property-read int|null $messages_count
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Mailbox newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Mailbox newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Mailbox query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Mailbox whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Mailbox whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Mailbox whereEncryption($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Mailbox whereHost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Mailbox whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Mailbox whereIgnoredFolders($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Mailbox wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Mailbox wherePort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Mailbox whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Mailbox whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Mailbox whereUsername($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Mailbox whereValidateCert($value)
 */
	class Mailbox extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Message
 *
 * @property int $id
 * @property int|null $uid
 * @property string|null $subject
 * @property array $from
 * @property array $to
 * @property array $cc
 * @property array $bcc
 * @property mixed $attachments
 * @property array $labels
 * @property string|null $text_body
 * @property string|null $html_body
 * @property string|null $processed_at
 * @property string|null $received_at
 * @property int $mailbox_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $searchable
 * @property-read mixed $from_emails
 * @property-read mixed $to_emails
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\MediaLibrary\Models\Media[] $images
 * @property-read int|null $images_count
 * @property-read \App\Models\Mailbox $mailbox
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Media[] $media
 * @property-read int|null $media_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message whereAttachments($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message whereBcc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message whereCc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message whereFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message whereHtmlBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message whereLabels($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message whereMailboxId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message whereProcessedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message whereReceivedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message whereSearchable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message whereSubject($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message whereTextBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message whereTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message whereUpdatedAt($value)
 */
	class Message extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Team
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $logo
 * @property int|null $owner_id
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read int $project_count
 * @property-read int $user_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Team[] $invites
 * @property-read int|null $invites_count
 * @property-read \App\Models\User $owner
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Project[] $projects
 * @property-read int|null $projects_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Team filter($filters)
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Team newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Team newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Team onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Team query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Team whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Team whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Team whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Team whereLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Team whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Team whereOwnerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Team whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Team whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Team withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Team withoutTrashed()
 */
	class Team extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Project
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property int|null $team_id
 * @property int $user_id
 * @property string|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Team|null $team
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Project newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Project newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Project query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Project whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Project whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Project whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Project whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Project whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Project whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Project whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Project whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Project whereUserId($value)
 */
	class Project extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Invite
 *
 * @property int $id
 * @property string $code
 * @property string $email
 * @property int $team_id
 * @property int|null $sender_id
 * @property \Illuminate\Support\Carbon|null $claimed_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $inviteable
 * @property-read \App\Models\User $receiver
 * @property-read \App\Models\User|null $sender
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Invite newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Invite newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Invite onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Invite query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Invite whereClaimedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Invite whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Invite whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Invite whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Invite whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Invite whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Invite whereSenderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Invite whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Invite whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Invite withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Invite withoutTrashed()
 */
	class Invite extends \Eloquent {}
}

