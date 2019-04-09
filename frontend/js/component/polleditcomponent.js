class PollEditComponent extends Fronty.ModelComponent {

  constructor(pollsModel, userModel, router) {

    super(Handlebars.templates.polledit, pollsModel);
    this.pollsModel = pollsModel; // polls
    this.userModel = userModel; // global
    this.addModel('user', userModel);
    this.router = router;

    this.pollsService = new PollsService();

    this.addEventListener('click', '#savebutton', () => {
      
      this.pollsModel.selectedPoll.title = $('#title').val();
      var checkbox = document.getElementById("checkbox").checked;

      if(checkbox == false){

        this.pollsModel.selectedPoll.anonymous = 0;
      }
      else{
        this.pollsModel.selectedPoll.anonymous = 1;
      }
      
      this.pollsService.savePoll(this.pollsModel.selectedPoll)
        .then(() => {
          this.pollsModel.set((model) => {
            model.errors = []
          });
          this.router.goToPage('polls');
        })
        .fail((xhr, errorThrown, statusText) => {
          if (xhr.status == 400) {
            this.pollsModel.set((model) => {
              model.errors = xhr.responseJSON;
            });
          } else {
            alert('an error has occurred during request: ' + statusText + '.' + xhr.responseText);
          }
        });

    });
}

onStart() {
  var selectedId = this.router.getRouteQueryParam('id');
  if (selectedId != null) {
    this.pollsService.findPoll(selectedId)
      .then((poll) => {
        this.pollsModel.setSelectedPoll(poll);
      });
  }
}
}
