class PollModel extends Fronty.Model {

    constructor(id, title, link, author_email) {

      super('PollModel'); //call super
      
      if (id) {

        this.id = id;
      }
      
      if (title) {

        this.title = title;
      }

      if (link) {
          
        this.link = link;
      }
      
      if (author_email) {

        this.author_email = author_email;
      }
    }
  
    setTitle(title) {

      this.set((self) => {

        self.title = title;
      });
    }

    setLink(link) {

        this.set((self) => {

          self.link = link;
        });
      }
  
    setEmail(author_email) {

      this.set((self) => {
          
        self.author_email = author_email;
      });
    }
  }
  