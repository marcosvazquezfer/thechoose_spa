class RemoveUsersComponent extends Fronty.ModelComponent {

  constructor(pollsModel, userModel, router) {

    super(Handlebars.templates.removeuserstable, pollsModel);

    this.pollsModel = pollsModel; // polls
    this.userModel = userModel; // global
    this.addModel('user', userModel);
    this.router = router;

    this.pollsService = new PollsService();

    this.addEventListener('click', '#remove-user-button', () => {
        
      var check = document.getElementsByName('checkbox[]');
      var i;
      for(i = 0; i < check.length; i++){

        var participantId = check[i].value;
        var pollId = this.router.getRouteQueryParam('id');
        
        if(check[i].checked == true){

          this.pollsService.removeUser(pollId, participantId)
            .then(() => {
              this.router.goToPage('view-poll?id=' + pollId);
            })
            .fail((xhr, errorThrown, statusText) => {
              if (xhr.status == 400) {
                this.pollsModel.set(() => {
                  this.pollsModel.errors = xhr.responseJSON;
                });
              } else {
                alert('an error has occurred during request: ' + statusText + '.' + xhr.responseText);
              }
            });
        }
        else{
            this.router.goToPage('view-poll?id=' + pollId);
        };
      }
    });
  }

  onStart() {

    var selectedId = this.router.getRouteQueryParam('id');
    this.loadPoll(selectedId);
  }

  loadPoll(pollId) {

    if (pollId != null) {

      this.pollsService.findPoll(pollId)
        .then((poll) => {
          this.pollsModel.setSelectedPoll(poll);
      });
    }
  }
}
