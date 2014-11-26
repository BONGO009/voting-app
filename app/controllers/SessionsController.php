<?php

class SessionsController extends \BaseController {

  protected $sessionsValidator;

  public function __construct(UserSessionValidator $userSessionValidator, AdminSessionValidator $adminSessionValidator, UserRegistrationValidator $registrationValidator)
  {
    $this->userSessionValidator = $userSessionValidator;
    $this->adminSessionValidator = $adminSessionValidator;
    $this->registrationValidator = $registrationValidator;
  }


  /**
   * Show the form for creating a new resource.
   * GET /login
   *
   * @return Response
   */
  public function create()
  {
    return View::make('sessions.create');
  }

  /**
   * Allows an admin to log in.
   * GET /admin
   *
   * @return Response
   */
  public function adminCreate()
  {
    return View::make('sessions.admin');
  }

  /**
   * Store a newly created resource in storage.
   * POST /sessions
   *
   * @return Response
   */
  public function store()
  {
    // If coming from admin, use that login method.
    if (Input::has('password')) {
      $input = Input::only('email', 'password');
      $this->adminSessionValidator->validate($input);
      return $this->adminLogin($input);
    }
    // Use the user login/create method.
    else {
      $input = Input::only('first_name', 'email', 'phone', 'birthdate', 'candidate_id');
      $this->userSessionValidator->validate($input);
      return $this->userLogin($input);
    }

  }

  /**
   * Remove the specified resource from storage.
   * DELETE /sessions/{id}
   *
   * @param  int  $id
   * @return Response
   */
  public function destroy($id = null)
  {
    Auth::logout();

    return Redirect::home()->withFlashMessage('You\'re now signed out.');;
  }

  /**
   * Authentication for a non-admin user.
   * See create() above.
   *
   */
  public function userLogin($input)
  {
    $user = User::isCurrentUser($input);

    if (!$user) {
      $this->registrationValidator->validate($input);
      $user = User::createNewUser($input);
    }

    // Log in the user.
    Auth::login($user);

    // Is the user login on a vote page?
    if (isset($input['candidate_id']) && !empty($input['candidate_id'])) {
      $vote = Vote::createIfEligible($input['candidate_id'], $user->id);

      if ($vote) {
        $candidate = Candidate::find($input['candidate_id']);
        $url = URL::route('candidates.show', [$candidate->slug, '#message']);

        return Redirect::to($url)->withFlashMessage('Welcome ' . $input['first_name'] . '. We got that vote!');
      } else {
        return Redirect::back()->withFlashMessage('Welcome back ' . $input['first_name'] . '. You already voted in that category today!');
      }
    }

    return Redirect::intended('/')->withFlashMessage('Welcome ' . $input['first_name']);
  }

  /**
   *
   */
  public function adminLogin($input)
  {
    // $this->sessionValidator->validate($input);
    if(Auth::attempt($input)) {
      return Redirect::intended('/')->withFlashMessage('Welcome back!');
    }
    else {
      return Redirect::back()->withInput()->withFlashMessage('Invalid username or password!');
    }

  }

}
