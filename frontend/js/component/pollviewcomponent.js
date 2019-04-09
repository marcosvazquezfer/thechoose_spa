class PollViewComponent extends Fronty.ModelComponent {

    constructor(pollsModel, userModel, router) {

      super(Handlebars.templates.pollview, pollsModel);
  
      this.pollsModel = pollsModel; // polls
      this.userModel = userModel; // global
      this.addModel('user', userModel);
      this.router = router;
  
      this.pollsService = new PollsService();
  
      this.addEventListener('click', '#savecommentbutton', () => {
          
        var selectedId = this.router.getRouteQueryParam('id');
        this.pollsService.createComment(selectedId, {
            content: $('#commentcontent').val()
          })
          .then(() => {
            $('#commentcontent').val('');
            this.loadPoll(selectedId);
          })
          .fail((xhr, errorThrown, statusText) => {
            if (xhr.status == 400) {
              this.pollsModel.set(() => {
                this.pollsModel.commentErrors = xhr.responseJSON;
              });
            } else {
              alert('an error has occurred during request: ' + statusText + '.' + xhr.responseText);
            }
          });
      });
    }
  
    onStart() {

      var selectedId = this.router.getRouteQueryParam('id');

      if(this.userModel.isLogged == false){

        this.pollsService.findAnonymous(selectedId)
          .then((poll) => {
            if(poll.anonymous == 1){
              this.loadAnonymousPoll(selectedId);
            }
            else{
              this.loadPoll(selectedId);
            }
          });
      }
      else{
        this.loadPoll(selectedId);
      }
    }
  
    loadPoll(pollId) {

      if (pollId != null) {

        this.pollsService.findPoll(pollId)
          .then((poll) => {
            this.pollsModel.setSelectedPoll(poll);
          });
      }
    }

    loadAnonymousPoll(pollId) {

      if (pollId != null) {

        this.pollsService.findAnonymousPoll(pollId)
          .then((poll) => {
            this.pollsModel.setSelectedPoll(poll);
          });
      }
    }
  }
  