/* Main mvcblog-front script */

//load external resources
function loadTextFile(url) {
  
  return new Promise((resolve, reject) => {
    $.get({
      url: url,
      cache: true,
      dataType: 'text'
    }).then((source) => {
      resolve(source);
    }).fail(() => reject());
  });
}


// Configuration
var AppConfig = {
  backendServer: 'http://localhost:8081'
  //backendServer: '/mvcblog'
}

Handlebars.templates = {};
Promise.all([
    I18n.initializeCurrentLanguage('js/i18n'),
    loadTextFile('templates/components/main.hbs').then((source) =>
      Handlebars.templates.main = Handlebars.compile(source)),
      loadTextFile('templates/components/index.hbs').then((source) =>
      Handlebars.templates.index = Handlebars.compile(source)),
    loadTextFile('templates/components/language.hbs').then((source) =>
      Handlebars.templates.language = Handlebars.compile(source)),
    loadTextFile('templates/components/user.hbs').then((source) =>
      Handlebars.templates.user = Handlebars.compile(source)),
    loadTextFile('templates/components/remove-users-table.hbs').then((source) =>
      Handlebars.templates.removeuserstable = Handlebars.compile(source)),
    loadTextFile('templates/components/navigation.hbs').then((source) =>
      Handlebars.templates.navigation = Handlebars.compile(source)),
    loadTextFile('templates/components/login.hbs').then((source) =>
      Handlebars.templates.login = Handlebars.compile(source)),
    loadTextFile('templates/components/polls-table.hbs').then((source) =>
      Handlebars.templates.pollstable = Handlebars.compile(source)),
    loadTextFile('templates/components/poll-edit.hbs').then((source) =>
      Handlebars.templates.polledit = Handlebars.compile(source)),
    loadTextFile('templates/components/poll-view.hbs').then((source) =>
      Handlebars.templates.pollview = Handlebars.compile(source)),
    loadTextFile('templates/components/poll-participate.hbs').then((source) =>
      Handlebars.templates.pollparticipate = Handlebars.compile(source)),
    loadTextFile('templates/components/hole-add.hbs').then((source) =>
      Handlebars.templates.holeadd = Handlebars.compile(source)),
    loadTextFile('templates/components/hole-delete.hbs').then((source) =>
      Handlebars.templates.holedelete = Handlebars.compile(source)),
    loadTextFile('templates/components/hole-choose.hbs').then((source) =>
      Handlebars.templates.holechoose = Handlebars.compile(source)),
    loadTextFile('templates/components/hole-edit.hbs').then((source) =>
      Handlebars.templates.holeedit = Handlebars.compile(source)),
    loadTextFile('templates/components/poll-row.hbs').then((source) =>
      Handlebars.templates.pollrow = Handlebars.compile(source))
  ])
  .then(() => {
    $(() => {
      new MainComponent().start();
    });
  }).catch((err) => {
    alert('FATAL: could not start app ' + err);
  });
