<?php

namespace App\Models;

use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Filesystem\AwsS3V3Adapter;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Class Document.
 *
 * @property int          $id
 * @property int|null     $public_id
 * @property int          $account_id
 * @property int          $user_id
 * @property int|null     $invoice_id
 * @property int|null     $expense_id
 * @property string       $path
 * @property string       $preview
 * @property string       $name
 * @property string       $type
 * @property string       $disk
 * @property string       $hash
 * @property int          $size
 * @property int|null     $width
 * @property int|null     $height
 * @property Carbon|null  $created_at
 * @property Carbon|null  $updated_at
 * @property int|null     $is_default
 * @property int          $is_proposal
 * @property string|null  $document_key
 * @property Account      $account
 * @property Expense|null $expense
 * @property Invoice|null $invoice
 * @property User         $user
 *
 * @method static Builder|Document newModelQuery()
 * @method static Builder|Document newQuery()
 * @method static Builder|Document proposalImages()
 * @method static Builder|Document query()
 * @method static Builder|Document scope(bool $publicId = false, bool $accountId = false)
 * @method static Builder|Document whereAccountId($value)
 * @method static Builder|Document whereCreatedAt($value)
 * @method static Builder|Document whereDisk($value)
 * @method static Builder|Document whereDocumentKey($value)
 * @method static Builder|Document whereExpenseId($value)
 * @method static Builder|Document whereHash($value)
 * @method static Builder|Document whereHeight($value)
 * @method static Builder|Document whereId($value)
 * @method static Builder|Document whereInvoiceId($value)
 * @method static Builder|Document whereIsDefault($value)
 * @method static Builder|Document whereIsProposal($value)
 * @method static Builder|Document whereName($value)
 * @method static Builder|Document wherePath($value)
 * @method static Builder|Document wherePreview($value)
 * @method static Builder|Document wherePublicId($value)
 * @method static Builder|Document whereSize($value)
 * @method static Builder|Document whereType($value)
 * @method static Builder|Document whereUpdatedAt($value)
 * @method static Builder|Document whereUserId($value)
 * @method static Builder|Document whereWidth($value)
 * @method static Builder|Document withActiveOrSelected($id = false)
 * @method static Builder|Document withArchived()
 *
 * @mixin \Eloquent
 */
class Document extends EntityModel
{
    /**
     * @var array
     */
    public static $extraExtensions = [
        'jpg' => 'jpeg',
        'tif' => 'tiff',
    ];

    /**
     * @var array
     */
    public static $allowedMimes = [// Used by Dropzone.js; does not affect what the server accepts
        'image/png', 'image/jpeg', 'image/tiff', 'application/pdf', 'image/gif', 'image/vnd.adobe.photoshop', 'text/plain',
        'application/msword',
        'application/excel', 'application/vnd.ms-excel', 'application/x-excel', 'application/x-msexcel',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/postscript',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation', 'application/vnd.ms-powerpoint',
    ];

    /**
     * @var array
     */
    public static $types = [
        'png' => [
            'mime' => 'image/png',
        ],
        'ai' => [
            'mime' => 'application/postscript',
        ],
        'jpeg' => [
            'mime' => 'image/jpeg',
        ],
        'tiff' => [
            'mime' => 'image/tiff',
        ],
        'pdf' => [
            'mime' => 'application/pdf',
        ],
        'gif' => [
            'mime' => 'image/gif',
        ],
        'psd' => [
            'mime' => 'image/vnd.adobe.photoshop',
        ],
        'txt' => [
            'mime' => 'text/plain',
        ],
        'doc' => [
            'mime' => 'application/msword',
        ],
        'xls' => [
            'mime' => 'application/vnd.ms-excel',
        ],
        'ppt' => [
            'mime' => 'application/vnd.ms-powerpoint',
        ],
        'xlsx' => [
            'mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ],
        'docx' => [
            'mime' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ],
        'pptx' => [
            'mime' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        ],
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'invoice_id',
        'expense_id',
        'is_default',
    ];

    /**
     * @param      $path
     * @param      $disk
     * @param bool $prioritizeSpeed
     *
     * @throws \OpenCloud\Common\Exceptions\NoNameError
     *
     * @return null|string
     */
    public static function getDirectFileUrl($path, $disk, $prioritizeSpeed = false)
    {
        $adapter = $disk->getAdapter();
        $fullPath = $adapter->applyPathPrefix($path);

        if ($adapter instanceof AwsS3V3Adapter) {
            $client = $adapter->getClient();
            $command = $client->getCommand('GetObject', [
                'Bucket' => $adapter->getBucket(),
                'Key'    => $fullPath,
            ]);

            return (string) $client->createPresignedRequest($command, '+10 minutes')->getUri();
        }

        if ( ! $prioritizeSpeed // Rackspace temp URLs are slow, so we don't use them for previews
                   && $adapter instanceof RackspaceAdapter) {
            $secret = env('RACKSPACE_TEMP_URL_SECRET');
            if ($secret) {
                $object = $adapter->getContainer()->getObject($fullPath);

                if (env('RACKSPACE_TEMP_URL_SECRET_SET')) {
                    // Go ahead and set the secret too
                    $object->getService()->getAccount()->setTempUrlSecret($secret);
                }

                $url = $object->getUrl();
                $expiry = strtotime('+10 minutes');
                $urlPath = urldecode($url->getPath());
                $body = sprintf("%s\n%d\n%s", 'GET', $expiry, $urlPath);
                $hash = hash_hmac('sha1', $body, $secret);

                return sprintf('%s?temp_url_sig=%s&temp_url_expires=%d', $url, $hash, $expiry);
            }
        }
    }

    public function getEntityType(): string
    {
        return ENTITY_DOCUMENT;
    }

    /**
     * @param array $attributes
     *
     * @return $this
     */
    public function fill(array $attributes)
    {
        parent::fill($attributes);

        if (empty($this->attributes['disk'])) {
            $this->attributes['disk'] = env('DOCUMENT_FILESYSTEM', 'documents');
        }

        return $this;
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function expense()
    {
        return $this->belongsTo(Expense::class)->withTrashed();
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class)->withTrashed();
    }

    public function getDisk()
    {
        return Storage::disk(empty($this->disk) ? env('DOCUMENT_FILESYSTEM', 'documents') : $this->disk);
    }

    /**
     * @param $value
     */
    public function setDiskAttribute($value): void
    {
        $this->attributes['disk'] = $value ?: env('DOCUMENT_FILESYSTEM', 'documents');
    }

    /**
     * @return null|string
     */
    public function getDirectUrl()
    {
        return static::getDirectFileUrl($this->path, $this->getDisk());
    }

    /**
     * @return null|string
     */
    public function getDirectPreviewUrl()
    {
        return $this->preview ? static::getDirectFileUrl($this->preview, $this->getDisk(), true) : null;
    }

    public function getRaw()
    {
        $disk = $this->getDisk();

        return $disk->get($this->path);
    }

    public function getRawCached()
    {
        $key = 'image:' . $this->path;

        if ($image = cache($key)) {
            // do nothing
        } else {
            $image = $this->getRaw();
            cache([$key => $image], 120);
        }

        return $image;
    }

    public function getStream()
    {
        $disk = $this->getDisk();

        return $disk->readStream($this->path);
    }

    public function getRawPreview()
    {
        $disk = $this->getDisk();

        return $disk->get($this->preview);
    }

    /**
     * @return UrlGenerator|string
     */
    public function getUrl()
    {
        return url('documents/' . $this->public_id . '/' . $this->name);
    }

    /**
     * @param $invitation
     *
     * @return UrlGenerator|string
     */
    public function getClientUrl($invitation)
    {
        return url('client/documents/' . $invitation->invitation_key . '/' . $this->public_id . '/' . $this->name);
    }

    public function getProposalUrl()
    {
        if ( ! $this->is_proposal || ! $this->document_key) {
            return '';
        }

        return url('proposal/image/' . $this->account->account_key . '/' . $this->document_key . '/' . $this->name);
    }

    public function isPDFEmbeddable(): bool
    {
        return $this->type == 'jpeg' || $this->type == 'png' || $this->preview;
    }

    /**
     * @return UrlGenerator|null|string
     */
    public function getVFSJSUrl()
    {
        if ( ! $this->isPDFEmbeddable()) {
            return;
        }

        return url('documents/js/' . $this->public_id . '/' . $this->name . '.js');
    }

    /**
     * @return UrlGenerator|null|string
     */
    public function getClientVFSJSUrl()
    {
        if ( ! $this->isPDFEmbeddable()) {
            return;
        }

        return url('client/documents/js/' . $this->public_id . '/' . $this->name . '.js');
    }

    /**
     * @return UrlGenerator|null|string
     */
    public function getPreviewUrl()
    {
        return $this->preview ? url('documents/preview/' . $this->public_id . '/' . $this->name . '.' . pathinfo($this->preview, PATHINFO_EXTENSION)) : null;
    }

    public function toArray()
    {
        $array = parent::toArray();

        if (empty($this->visible) || in_array('url', $this->visible)) {
            $array['url'] = $this->getUrl();
        }

        if (empty($this->visible) || in_array('preview_url', $this->visible)) {
            $array['preview_url'] = $this->getPreviewUrl();
        }

        return $array;
    }

    public function cloneDocument()
    {
        $document = self::createNew($this);
        $document->path = $this->path;
        $document->preview = $this->preview;
        $document->name = $this->name;
        $document->type = $this->type;
        $document->disk = $this->disk;
        $document->hash = $this->hash;
        $document->size = $this->size;
        $document->width = $this->width;
        $document->height = $this->height;

        return $document;
    }

    public function scopeProposalImages($query)
    {
        return $query->whereIsProposal(1);
    }
}

Document::deleted(function ($document): void {
    $same_path_count = DB::table('documents')
        ->where('documents.account_id', '=', $document->account_id)
        ->where('documents.path', '=', $document->path)
        ->where('documents.disk', '=', $document->disk)
        ->count();

    if ( ! $same_path_count) {
        $document->getDisk()->delete($document->path);
    }

    if ($document->preview) {
        $same_preview_count = DB::table('documents')
            ->where('documents.account_id', '=', $document->account_id)
            ->where('documents.preview', '=', $document->preview)
            ->where('documents.disk', '=', $document->disk)
            ->count();
        if ( ! $same_preview_count) {
            $document->getDisk()->delete($document->preview);
        }
    }
});
