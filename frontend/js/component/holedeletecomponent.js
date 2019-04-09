class HoleDeleteComponent extends Fronty.ModelComponent {

    constructor(holesModel, userModel, router) {
        
      super(Handlebars.templates.holedelete, holesModel);
      this.holesModel = holesModel; // holes
      this.userModel = userModel; // global
      this.addModel('user', userModel);
      this.router = router;
  
      this.pollsService = new PollsService();

      this.addEventListener('click', '#deletebutton', () => {

        var selection = document.getElementById('hole');
        var hole = selection.value;
        var date = hole.substr(0, 10);
        //alert(date);
        var time = hole.substr(11, 28);
        //alert(time);
        var timeStart = time.substr(0, 8);
        //alert(timeStart);
        var timeFinish = time.substr(9, 17);
        //alert(timeFinish);

        var hole = {};

        hole.pollId = this.router.getRouteQueryParam('id');
        //alert(hole.pollId);
        hole.date = date;
        //alert(hole.date);
        hole.timeStart = timeStart;
        //alert(hole.timeStart);
        hole.timeFinish = timeFinish;
        //alert(hole.timeFinish);
        //alert(JSON.stringify(hole));

        this.pollsService.deleteHole(hole).then(() => {

          this.router.goToPage('edit-poll?id=' + hole.pollId);
        })
        .fail((xhr, errorThrown, statusText) => {

          if (xhr.status == 400) {

            this.pollsModel.set(() => {

              this.pollsModel.errors = xhr.responseJSON;
            });
          } 
          else {
            alert('an error has occurred during request: ' + statusText + '.' + xhr.responseText);
          }
        });
      });
    }
  
    onStart() {

      this.updateHoles();
    }

    updateHoles(){

      var selectedId = this.router.getRouteQueryParam('id');
      
      if (selectedId != null) {

        this.pollsService.findHoles(selectedId).then((data) => {
    
          this.holesModel.setHoles(
            // create a Fronty.Model for each item retrieved from the backend

            data.map(
              (item) => new HoleModel(item.pollId, item.date, item.timeStart, item.timeFinish)
          ));
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
    }
  }
  