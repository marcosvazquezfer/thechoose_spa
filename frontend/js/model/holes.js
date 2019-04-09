class HolesModel extends Fronty.Model {

  constructor() {
    
    super('HolesModel'); //call super

    // model attributes
    this.holes = [];
  }

  setSelectedHole(hole) {

    this.set((self) => {
      self.selectedHole = hole;
    });
  }

  setHoles(holes) {
      
    this.set((self) => {
      self.holes = holes;
    });
  }
}
