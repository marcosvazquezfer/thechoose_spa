class HoleAddComponent extends Fronty.ModelComponent {

    constructor(holesModel, userModel, router) {
  
      super(Handlebars.templates.holeadd, holesModel);
  
      this.holesModel = holesModel; // polls
      
      this.userModel = userModel; // global
      this.addModel('user', userModel);
      this.router = router;
  
      this.pollsService = new PollsService();
  
      this.addEventListener('click', '#savebutton', () => {

        var newHole = {};
        
        newHole.pollId = this.router.getRouteQueryParam('id');
        newHole.date = $('#date').val();
        newHole.timeStart = $('#timeStart').val();
        newHole.timeFinish = $('#timeFinish').val();
        this.pollsService.addHole(newHole)
          .then(() => {
            this.router.goToPage('edit-poll?id=' + newHole.pollId);
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
      });
    }
    
    onStart() {
      this.holesModel.setSelectedHole(new HoleModel());
    }
  }
  