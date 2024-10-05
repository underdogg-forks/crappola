<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateDocumentRequest;
use App\Http\Requests\DocumentRequest;
use App\Http\Requests\UpdateDocumentRequest;
use App\Models\Document;
use App\Ninja\Repositories\DocumentRepository;
use Response;

class DocumentController extends BaseController
{
    protected $documentRepo;

    protected $entityType = ENTITY_DOCUMENT;

    public function __construct(DocumentRepository $documentRepo)
    {
        // parent::__construct();

        $this->documentRepo = $documentRepo;
    }

    public static function getDownloadResponse($document)
    {
        $direct_url = $document->getDirectUrl();
        if ($direct_url) {
            return redirect($direct_url);
        }

        $stream = $document->getStream();

        if ($stream) {
            $headers = [
                'Content-Type'   => Document::$types[$document->type]['mime'],
                'Content-Length' => $document->size,
            ];

            $response = \Illuminate\Support\Facades\Response::stream(function () use ($stream): void {
                fpassthru($stream);
            }, 200, $headers);
        } else {
            $response = \Illuminate\Support\Facades\Response::make($document->getRaw(), 200);
            $response->header('content-type', Document::$types[$document->type]['mime']);
        }

        return $response;
    }

    public function get(DocumentRequest $request)
    {
        return static::getDownloadResponse($request->entity());
    }

    public function getPreview(DocumentRequest $request)
    {
        $document = $request->entity();

        if (empty($document->preview)) {
            return \Illuminate\Support\Facades\Response::view('error', ['error' => 'Preview does not exist!'], 404);
        }

        $direct_url = $document->getDirectPreviewUrl();
        if ($direct_url) {
            return redirect($direct_url);
        }

        $previewType = pathinfo($document->preview, PATHINFO_EXTENSION);
        $response = \Illuminate\Support\Facades\Response::make($document->getRawPreview(), 200);
        $response->header('content-type', Document::$types[$previewType]['mime']);

        return $response;
    }

    public function getVFSJS(DocumentRequest $request, $publicId, $name)
    {
        $document = $request->entity();

        if (mb_substr($name, -3) == '.js') {
            $name = mb_substr($name, 0, -3);
        }

        if ( ! $document->isPDFEmbeddable()) {
            return \Illuminate\Support\Facades\Response::view('error', ['error' => 'Image does not exist!'], 404);
        }

        $content = $document->preview ? $document->getRawPreview() : $document->getRaw();
        $content = 'ninjaAddVFSDoc(' . json_encode((int) $publicId . '/' . (string) $name) . ',"' . base64_encode($content) . '")';
        $response = \Illuminate\Support\Facades\Response::make($content, 200);
        $response->header('content-type', 'text/javascript');
        $response->header('cache-control', 'max-age=31536000');

        return $response;
    }

    public function postUpload(CreateDocumentRequest $request)
    {
        $result = $this->documentRepo->upload($request->all(), $doc_array);

        if (is_string($result)) {
            return \Illuminate\Support\Facades\Response::json([
                'error' => $result,
                'code'  => 400,
            ], 400);
        }
        if ($request->grapesjs) {
            $response = [
                'data' => [
                    $result->getProposalUrl(),
                ],
            ];
        } else {
            $response = [
                'error'    => false,
                'document' => $doc_array,
                'code'     => 200,
            ];
        }

        return \Illuminate\Support\Facades\Response::json($response, 200);
    }

    public function delete(UpdateDocumentRequest $request)
    {
        $request->entity()->delete();

        return RESULT_SUCCESS;
    }
}
