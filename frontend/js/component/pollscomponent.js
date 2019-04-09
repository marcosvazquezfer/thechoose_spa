class PollsComponent extends Fronty.ModelComponent {

    constructor(pollsModel, userModel, router) {
  
      super(Handlebars.templates.pollstable, pollsModel, null, null);
      
      
      this.pollsModel = pollsModel;
      this.userModel = userModel;
      this.addModel('user', userModel);
      this.router = router;
  
      this.pollsService = new PollsService();
  
      this.addEventListener('click', '#add-poll-button', ()=>{
       
        this.pollsService.addPoll().then((newpoll)=>{
          
          this.router.goToPage('edit-poll?id='+newpoll.pollId)
        });
      });

    }
  
    onStart() {
  
      this.updatePolls();

    }
  
    updatePolls() {
      
      this.pollsService.findMyPolls().then((data) => {
  
        var polls = [];
        data.forEach((item)=>{
          if (polls.find((pollModel)=> pollModel.id === item.pollId) === undefined) {
            polls.push( new PollModel(item.pollId, item.title, item.link, item.author_email));
          }
        })
        this.pollsModel.setPolls(polls);
        /*this.pollsModel.setPolls(
          // create a Fronty.Model for each item retrieved from the backend

          data.map(
            (item) => new PollModel(item.pollId, item.title, item.link, item.author_email)
        ));*/
      });
    }
  
    // Override
    createChildModelComponent(className, element, id, modelItem) {
      //alert('a');
      return new PollRowComponent(modelItem, this.userModel, this.router, this);
    }
  }
  
  class PollRowComponent extends Fronty.ModelComponent {

    constructor(pollModel, userModel, router, pollsComponent) {
      //alert('b');
      super(Handlebars.templates.pollrow, pollModel, null, null);
      
      this.pollsComponent = pollsComponent;
      
      this.userModel = userModel;
      this.addModel('user', userModel); // a secondary model
      
      this.router = router;
  
      this.addEventListener('click', '.delete-poll-button', (event) => {
        if (confirm(I18n.translate('Are you sure?'))) {
          var pollId = event.target.getAttribute('item');
          this.pollsComponent.pollsService.deletePoll(pollId)
            .fail(() => {
              alert('poll cannot be deleted')
            })
            .always(() => {
              this.pollsComponent.updatePolls();
            });
        }
      });
  
      this.addEventListener('click', '.unsubscribe-poll-button', (event) => {
        if (confirm(I18n.translate('Are you sure?'))) {
          var pollId = event.target.getAttribute('item');
          this.pollsComponent.pollsService.unsubscribeUser(pollId)
            .fail(() => {
              alert('poll cannot be deleted')
            })
            .always(() => {
              this.pollsComponent.updatePolls();
            });
        }
      });
    }
  
  }
  