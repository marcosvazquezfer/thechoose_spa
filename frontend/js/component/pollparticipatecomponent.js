class PollParticipateComponent extends Fronty.ModelComponent {

    constructor(pollsModel, userModel, router) {

      super(Handlebars.templates.pollparticipate, pollsModel);
  
      this.pollsModel = pollsModel; // polls
      this.userModel = userModel; // global
      this.addModel('user', userModel);
      this.router = router;
  
      this.pollsService = new PollsService();

      

      this.addEventListener('click', '#submitbutton', () => {

        var check = document.getElementsByName('checkbox[]');
        var i;

        if(this.userModel.isLogged == false){

          var name;

          do{
            name = prompt("Introduce tu nombre completo", "");
            this.userService = new UserService();

            this.userService.registerAnonymous({
              completeName: name,
              pollId : this.router.getRouteQueryParam('id')
            })
            .fail((xhr, errorThrown, statusText) => {
              if (xhr.status == 400) {
                this.userModel.set(() => {
                  this.userModel.registerErrors = xhr.responseJSON;
                });
              } else {
                alert('an error has occurred during request: ' + statusText + '.' + xhr.responseText);
              }
            });
          }while(name == null);

          alert('Su nombre se ha registrado correctamente');

          for(i = 0; i < check.length; i++){

            var value = check[i].value;
            var date = value.substr(0, 10);
            var timeStart = value.substr(11, 19);
            
            if(check[i].checked == true){

              var newSelection = {};
          
              newSelection.pollId = this.router.getRouteQueryParam('id');
              //alert(newSelection.pollId);
              newSelection.date = date;
              //alert(newSelection.date);
              newSelection.timeStart = timeStart;
              //alert(newSelection.timeStart);
              newSelection.selection = "1";
              //alert(newSelection.selectioln);
              //alert(JSON.stringify(newSelection));

              newSelection.completeName = name;

              this.pollsService.addAnonymousSelection(newSelection)
              .then(() => {
                this.router.goToPage('view-poll?id=' + newSelection.pollId);
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
              var newSelection = {};
          
              newSelection.pollId = this.router.getRouteQueryParam('id');
              //alert(newSelection.pollId);
              newSelection.date = date;
              //alert(newSelection.date);
              newSelection.timeStart = timeStart;
              newSelection.selection = "0";
              //alert(newSelection.timeStart);

              newSelection.completeName = name;

              this.pollsService.addAnonymousSelection(newSelection)
              .then(() => {
                this.router.goToPage('view-poll?id=' + newSelection.pollId);
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
        else{
          for(i = 0; i < check.length; i++){

            var value = check[i].value;
            var date = value.substr(0, 10);
            var timeStart = value.substr(11, 19);
            
            if(check[i].checked == true){

              var newSelection = {};
          
              newSelection.pollId = this.router.getRouteQueryParam('id');
              //alert(newSelection.pollId);
              newSelection.date = date;
              //alert(newSelection.date);
              newSelection.timeStart = timeStart;
              //alert(newSelection.timeStart);
              newSelection.selection = "1";
              //alert(newSelection.selectioln);
              //alert(JSON.stringify(newSelection));

              this.pollsService.addSelection(newSelection)
                .then(() => {
                  this.router.goToPage('view-poll?id=' + newSelection.pollId);
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
              var newSelection = {};
          
              newSelection.pollId = this.router.getRouteQueryParam('id');
              //alert(newSelection.pollId);
              newSelection.date = date;
              //alert(newSelection.date);
              newSelection.timeStart = timeStart;
              newSelection.selection = "0";
              //alert(newSelection.timeStart);

              this.pollsService.addSelection(newSelection)
                .then(() => {
                  this.router.goToPage('view-poll?id=' + newSelection.pollId);
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
      });
    }
  
    onStart() {

      var selectedId = this.router.getRouteQueryParam('id');

      if(this.userModel.isLogged == false){

        this.loadAnonymousPoll(selectedId);
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
  