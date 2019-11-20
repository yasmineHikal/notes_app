<?php

namespace App\Http\Controllers\API;

use App\Events\CreatedNoteEvent;
use App\Note;
use App\Notifications\NoteCreated;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class NoteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return Note::latest()->paginate(5);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|string|max:191',
            'description' => 'required|string|min:5',
        ]);
        $note = Note::create([
            'title' => $request['title'],
            'description' => $request['description'],
            'user_id' => auth('api')->user()->id
        ]);

        $users = User::all();
        foreach ($users as $user) {
            $user->notify(new NoteCreated($note));

        }

        return $note;
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $note = Note::findOrFail($id);
        $this->validate($request, [
            'title' => 'required|string|max:191',
            'description' => 'required|string|min:5',
        ]);
        $note->update($request->all());

        return ['message' => 'Updated the note'];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $note = Note::findOrFail($id);
        $note->delete();
        return ['message' => 'User deleted'];
    }


    public function authUser()
    {
        return auth('api')->user();
    }

    public function getNotification()
    {
        $notification = Auth::user()->unreadNotifications->take(5);
        return $notification;

    }


}
