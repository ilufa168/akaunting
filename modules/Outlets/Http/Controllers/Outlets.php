<?php

namespace Modules\Outlets\Http\Controllers;

use App\Abstracts\Http\Controller;
use Illuminate\Http\Request;
use Modules\Outlets\Jobs\CreateOutlet;
use Modules\Outlets\Jobs\DeleteOutlet;
use Modules\Outlets\Jobs\UpdateOutlet;
use Modules\Outlets\Models\Outlet;

class Outlets extends Controller
{
    /**
     * Instantiate a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('permission:create-outlets-main')->only('create', 'store', 'duplicate', 'import');
        $this->middleware('permission:read-outlets-main')->only('index', 'show', 'edit', 'export');
        $this->middleware('permission:update-outlets-main')->only('update', 'enable', 'disable');
        $this->middleware('permission:delete-outlets-main')->only('destroy');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $outlets = Outlet::collect();

        return $this->response('outlets::outlets.index', compact('outlets'));
    }

    /**
     * Show the form for viewing the specified resource.
     *
     * @param  Outlet  $outlet
     * @return \Illuminate\Http\Response
     */
    public function show(Outlet $outlet)
    {
        return view('outlets::outlets.show', compact('outlet'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('outlets::outlets.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $response = $this->ajaxDispatch(new CreateOutlet($request->merge(['enabled' => 1])));

        if ($response['success']) {
            $response['redirect'] = route('outlets.index');

            $message = trans('messages.success.created', ['type' => trans_choice('outlets::general.outlets', 1)]);

            flash($message)->success();
        } else {
            $response['redirect'] = route('outlets.create');

            $message = $response['message'];

            flash($message)->error()->important();
        }

        return response()->json($response);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Outlet  $outlet
     * @return \Illuminate\Http\Response
     */
    public function edit(Outlet $outlet)
    {
        return view('outlets::outlets.edit', compact('outlet'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Outlet  $outlet
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Outlet $outlet, Request $request)
    {
        $response = $this->ajaxDispatch(new UpdateOutlet($outlet, $request));

        if ($response['success']) {
            $response['redirect'] = route('outlets.index');

            $message = trans('messages.success.updated', ['type' => $outlet->name]);

            flash($message)->success();
        } else {
            $response['redirect'] = route('outlets.edit', $outlet->id);

            $message = $response['message'];

            flash($message)->error()->important();
        }

        return response()->json($response);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Outlet  $outlet
     * @return \Illuminate\Http\Response
     */
    public function destroy(Outlet $outlet)
    {
        $response = $this->ajaxDispatch(new DeleteOutlet($outlet));

        $response['redirect'] = route('outlets.index');

        if ($response['success']) {
            $message = trans('messages.success.deleted', ['type' => $outlet->name]);

            flash($message)->success();
        } else {
            $message = $response['message'];

            flash($message)->error()->important();
        }

        return response()->json($response);
    }
}
