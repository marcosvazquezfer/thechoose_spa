class HoleEditComponent extends Fronty.ModelComponent {

  constructor(holesModel, userModel, router) {
      
    super(Handlebars.templates.holeedit, holesModel);
    this.holesModel = holesModel; // holes
    this.userModel = userModel; // global
    this.addModel('user', userModel);
    this.router = router;

    this.pollsService = new PollsService();

    this.addEventListener('click', '#editbutton', () => {

      var newHole = {};
      
      newHole.pollId = this.router.getRouteQueryParam('id');
      newHole.date = $('#date').val();
      newHole.timeStart = $('#timeStart').val();
      newHole.timeFinish = $('#timeFinish').val();
      newHole.oldDate = document.getElementById("oldDate").value;
      newHole.oldTimeStart = document.getElementById("oldTimeStart").value;
      this.pollsService.editHole(newHole)
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

      var selectedId = this.router.getRouteQueryParam('id');
      var selectedDate = this.router.getRouteQueryParam('date');
      var selectedTimeStart = this.router.getRouteQueryParam('timeStart');

      var hole = new HoleModel(selectedId, selectedDate, selectedTimeStart);

      if (selectedId != null) {
        this.pollsService.findHole(hole)
          .then((hole) => {
            this.holesModel.setSelectedHole(hole);
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
