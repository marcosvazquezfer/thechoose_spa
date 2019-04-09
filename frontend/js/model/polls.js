class PollsModel extends Fronty.Model {

    constructor() {
      
      super('PollsModel'); //call super
  
      // model attributes
      this.polls = [];
    }
  
    setSelectedPoll(poll) {

      this.set((self) => {
        self.selectedPoll = poll;
      });
    }
  
    setPolls(polls) {
        
      this.set((self) => {
        self.polls = polls;
      });
    }
  }
  