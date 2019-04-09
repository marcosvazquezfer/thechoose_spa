class MainComponent extends Fronty.RouterComponent {

  constructor() {
    
    super('frontyapp', Handlebars.templates.main, 'maincontent');

    // models instantiation
    // we can instantiate models at any place
    var userModel = new UserModel();
    this.userModel = userModel;
    var pollsModel = new PollsModel();
    this.addModel('user', userModel);
    var holesModel = new HolesModel();

    super.setRouterConfig({

      polls: {
        
        component: new PollsComponent(pollsModel, userModel, this),
        title: 'Polls'
      },
      'view-poll': {
        component: new PollViewComponent(pollsModel, userModel, this),
        title: 'Poll'
      },
      'edit-poll': {
        component: new PollEditComponent(pollsModel, userModel, this),
        title: 'Edit Poll'
      },
      'add-hole': {
        component: new HoleAddComponent(holesModel, userModel, this),
        title: 'Add Hole'
      },
      'delete-hole': {
        component: new HoleDeleteComponent(holesModel, userModel, this),
        title: 'Delete Hole'
      },
      'choose-hole': {
        component: new HoleChooseComponent(holesModel, userModel, this),
        title: 'Choose Hole'
      },
      'edit-hole': {
        component: new HoleEditComponent(holesModel, userModel, this),
        title: 'Edit Hole'
      },
      'participate-poll': {
        component: new PollParticipateComponent(pollsModel, userModel, this),
        title: 'Participate Poll'
      },
      'editParticipation-poll': {
        component: new PollEditParticipationComponent(pollsModel, userModel, this),
        title: 'Edit Participation Poll'
      },
      'remove-users': {
        component: new RemoveUsersComponent(pollsModel, userModel, this),
        title: 'Remove Users'
      },
      login: {
        component: new LoginComponent(userModel, this),
        title: 'Login'
      },
      index: {
        component: new IndexComponent(),
        title: 'Welcome'
      },
      defaultRoute: 'index'
    });
    
    Handlebars.registerHelper('currentPage', () => {
          return super.getCurrentPage();
    });

    var userService = new UserService();
    this.addChildComponent(this._createUserBarComponent(userModel, userService));
    this.addChildComponent(this._createNavigationBarComponent(userModel, userService));
    this.addChildComponent(this._createLanguageComponent());

  }

  start() {
    // do relogin
    var userService = new UserService();
    userService.loginWithSessionData()
      .then((logged) => {
        if (logged != null) {
          this.userModel.setLoggeduser(logged);
        }
        super.start();

      });
  }

  _createUserBarComponent(userModel, userService) {

    var userbar = new Fronty.ModelComponent(Handlebars.templates.user, userModel, 'userbar');

    userbar.addEventListener('click', '#logoutbutton', () => {
      userModel.logout();
      userService.logout();
    });

    userbar.addEventListener('click', '#remove-user-button', () => {

      if (confirm(I18n.translate('Are you sure?'))) {
        userModel.logout();
        userService.removeUser()
          .fail(() => {
            alert('User cannot be deleted')
          });
      }
    });

    return userbar;
  }

  _createNavigationBarComponent(userModel, userService) {

    var navigationbar = new Fronty.ModelComponent(Handlebars.templates.navigation, userModel, 'navigationbar');

    return navigationbar;
  }

  _createLanguageComponent() {
    var languageComponent = new Fronty.ModelComponent(Handlebars.templates.language, this.routerModel, 'languagecontrol');
    // language change links
    languageComponent.addEventListener('click', '#englishlink', () => {
      I18n.changeLanguage('default');
      document.location.reload();
    });

    languageComponent.addEventListener('click', '#spanishlink', () => {
      I18n.changeLanguage('es');
      document.location.reload();
    });

    return languageComponent;
  }
}
