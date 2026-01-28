<?php

namespace App\Exceptions;

use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Str;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        $this->renderable(function (QueryException $e, $request) {
            if (config('app.debug')) {
                return null;
            }

            $message = $this->friendlyDatabaseMessage($e);

            if ($request->expectsJson()) {
                return response()->json(['message' => $message], 422);
            }

            if ($request->isMethod('get') || $request->isMethod('head')) {
                return response($message, 500);
            }

            return back()->withErrors([$message])->withInput();
        });
    }

    private function friendlyDatabaseMessage(QueryException $e): string
    {
        $rawMessage = $e->getMessage();

        if (preg_match("/Column '([^']+)' cannot be null/", $rawMessage, $matches)) {
            $field = $this->formatColumnName($matches[1]);
            return $field . ' is required.';
        }

        if (preg_match("/Duplicate entry '.*' for key '([^']+)'/", $rawMessage, $matches)) {
            $field = $this->formatKeyName($matches[1]);
            return $field . ' already exists.';
        }

        return 'Something went wrong while saving. Please review the form and try again.';
    }

    private function formatKeyName(string $key): string
    {
        $map = [
            'users_email_unique' => 'Email',
            'users_name_unique' => 'Username',
            'users_user_no_unique' => 'File No',
            'users_tel_no_unique' => 'Telephone No',
        ];

        if (isset($map[$key])) {
            return $map[$key];
        }

        $key = preg_replace('/^.*?_/', '', $key);
        $key = preg_replace('/_unique$/', '', $key);

        return $this->formatColumnName($key);
    }

    private function formatColumnName(string $column): string
    {
        $map = [
            'tel_no' => 'Telephone No',
            'mobile_no' => 'Telephone No 2',
            'user_no' => 'File No',
            'first_name' => 'First Name',
            'middle_name' => 'Middle Name',
            'last_name' => 'Last Name',
            'organization_id' => 'Organization',
            'salutation_id' => 'Designation',
        ];

        if (isset($map[$column])) {
            return $map[$column];
        }

        return Str::of($column)->replace('_', ' ')->title();
    }
}
