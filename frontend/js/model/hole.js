class HoleModel extends Fronty.Model {

  constructor(pollId, date, timeStart, timeFinish) {

    super('HoleModel'); //call super
    
    if (pollId) {

      this.pollId = pollId;
    }
    
    if (date) {

      this.date = date;
    }

    if (timeStart) {
        
      this.timeStart = timeStart;
    }
    
    if (timeFinish) {

      this.timeFinish = timeFinish;
    }
  }

  setPollId(pollId) {

    this.set((self) => {

      self.pollId = pollId;
    });
  }

  setDate(date) {

    this.set((self) => {

      self.date = date;
    });
  }

  setTimeStart(timeStart) {

      this.set((self) => {

        self.timeStart = timeStart;
      });
    }

  setTimeFinish(timeFinish) {

    this.set((self) => {
        
      self.timeFinish = timeFinish;
    });
  }
}
