<?php

namespace App\Http\Controllers;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

require_once ('SocietyWrapper.php');
require_once ('BookWrapper.php');


class BookController extends Controller
{
	public function listUsers(Request $request) {
        $users = \BookWrapper::getAllUsers();
        $userss = array();
        foreach ($users as $u) {
        	$user_id = $u->id;
            $user_name = $u->name;
            $user_email = $u->email;
            $user_year = $u->university_year;
            $us = array('user_name'=>($user_name), 'user_email'=>($user_email), 'user_year'=>($user_year), 'user_id'=>($user_id));
            array_push($userss, $us);
        }
    	return view('listAllUsers', ['users' => $userss]);
    }

    public function listAllBooks(Request $request) {
        // Current user would be the one making comments
        /*
        $bookss = \BookWrapper::getAllBooks();
        $books = array();
        foreach ($bookss as $book) {
            $society_id = $book->society_id;
            $society_name =\SocietyWrapper::getSocietyName($society_id);
            $booka = array('book_name'=>($book->book_name), 'society_name'=>($society_name), 'society_id'=>($society_id),
            'book_id'=>($book->id));
            array_push($books, $booka);
        }
    	return view('listAllBooks', ['books' => $books]);
    	*/
        $user_id = Auth::id();
    	$book_ids = \BookWrapper::getBooksOfUser($user_id);
        $bookss = \BookWrapper::getBooksFromIds($book_ids);
        $books = array();
        foreach ($bookss as $book) {
            $society_id = $book->society_id;
            $society_name =\SocietyWrapper::getSocietyName($society_id);
            $booka = array('book_name'=>($book->book_name), 'society_name'=>($society_name), 'society_id'=>($society_id),
            'book_id'=>($book->id));
            array_push($books, $booka);
        }
        
        $nbookss = \BookWrapper::getBooksUserDontHave($user_id);
        $nbooks = array();
        foreach ($nbookss as $book) {
            $society_id = $book->society_id;
            $society_name =\SocietyWrapper::getSocietyName($society_id);
            $booka = array('book_name'=>($book->book_name), 'society_name'=>($society_name), 'society_id'=>($society_id),
            'book_id'=>($book->id));
            array_push($nbooks, $booka);
        }
        
        return view('listAllBooks', ['nbooks' => $nbooks, 'books' => $books]);

    }
    
    public function listBookOwner(Request $request) {
        // Current user would be the one making comments
        $book_id = $request->input('book_id');
        $users_ids = \BookWrapper::getOwnersOfABook($book_id);
        $users = \BookWrapper::getUsersFromIds($users_ids);
        $userss = array();
        foreach ($users as $u) {
        	$user_id = $u->id;
            $user_name = $u->name;
            $user_email = $u->email;
            $user_year = $u->university_year;
            $us = array('user_name'=>($user_name), 'user_email'=>($user_email), 'user_year'=>($user_year), 'user_id'=>($user_id));
            array_push($userss, $us);
        }
    	return view('listAllUsersForBook', ['users' => $userss]);
    }
    
    
    public function addBook(Request $request) {
    	$societiess = \SocietyWrapper::getAllSocieties();
    	$societies = array();
    	
    	foreach ($societiess as $society) {
            $society_id = $society->id;
            $society_name = $society->name;
            $societya = array('society_id'=>($society_id), 'society_name'=>($society_name));
            array_push($societies, $societya);
        }
    	return view('bookCreation', ['societies' => $societies]);
    }
    
    public function create(Request $request) {
    	$title = $request->input('book_name');
        $society_id = $request->input('society');
        $book = \BookWrapper::insertBookToSociety($society_id, $title);
        
        $user_id = Auth::id();
        \BookWrapper::insertBookToUser($user_id, $book->id);
      
      
    	return redirect()->action('SocietyController@listUserSocieties');	
	}
	
	public function remove(Request $request)
    {
        $user_id = Auth::id();
        $book_id = $request->input('book_id');
        \BookWrapper::removeBookFromUser($user_id, $book_id);
        return redirect()->action(
            'SocietyController@listUserSocieties'
        );
    }
    
    public function remov(Request $request)
    {
        $user_id = Auth::id();
        $book_id = $request->input('book_id');
        \BookWrapper::removeBookFromUser($user_id, $book_id);
        return redirect()->action(
            'BookController@listAllBooks'
        );
    }
    
    public function add(Request $request)
    {
        $user_id = Auth::id();
        $book_id = $request->input('book_id');
        \BookWrapper::insertBookToUser($user_id, $book_id);
        return redirect()->action(
            'BookController@listAllBooks'
        );
    }
}