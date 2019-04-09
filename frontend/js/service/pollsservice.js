class PollsService {

    constructor() {
  
    }
  
    findMyPolls() {

      return $.get(AppConfig.backendServer+'/rest/poll');
    }
  
    findPoll(pollId) {

      return $.get(AppConfig.backendServer+'/rest/poll/' + pollId);
    }

    findHoles(pollId) {

      return $.get(AppConfig.backendServer+'/rest/poll/' + pollId + '/hole');
    }
  
    deletePoll(pollId) {

      return $.ajax({
        url: AppConfig.backendServer+'/rest/poll/' + pollId,
        method: 'DELETE'
      });
    }
  
    savePoll(poll) {

      return $.ajax({
        url: AppConfig.backendServer+'/rest/poll/' + poll.pollId,
        method: 'PUT',
        data: JSON.stringify(poll),
        contentType: 'application/json'
      });
    }
  
    addPoll() {
      return $.ajax({
        url: AppConfig.backendServer+'/rest/poll',
        method: 'POST'
      });
    }

    addHole(hole) {
      return $.ajax({
        url: AppConfig.backendServer+'/rest/poll/' + hole.pollId + '/hole',
        method: 'POST',
        data: JSON.stringify(hole),
        contentType: 'application/json'
      });
    }

    deleteHole(hole) {

      return $.ajax({
        url: AppConfig.backendServer+'/rest/poll/' + hole.pollId + '/hole',
        method: 'DELETE',
        data: JSON.stringify(hole),
        contentType: 'application/json'
      });
    }

    addSelection(selection) {

      return $.ajax({
        url: AppConfig.backendServer+'/rest/poll/' + selection.pollId + '/selection',
        method: 'PUT',
        data: JSON.stringify(selection),
        contentType: 'application/json'
      });
    }

    deletePoll(pollId) {

      return $.ajax({
        url: AppConfig.backendServer+'/rest/poll/' + pollId,
        method: 'DELETE'
      });
    }

    findHole(hole) {

      return $.get(AppConfig.backendServer+'/rest/poll/' + hole.pollId + '/hole/' + hole.date + '/' + hole.timeStart);
    }

    editHole(hole) {
      return $.ajax({
        url: AppConfig.backendServer+'/rest/poll/' + hole.pollId + '/hole',
        method: 'PUT',
        data: JSON.stringify(hole),
        contentType: 'application/json'
      });
    }

    unsubscribeUser(pollId) {

      return $.ajax({
        url: AppConfig.backendServer+'/rest/poll/' + pollId + '/user',
        method: 'DELETE'
      });
    }

    removeUser(pollId, participantId){

      return $.ajax({
        url: AppConfig.backendServer+'/rest/poll/' + pollId + '/user/' + participantId,
        method: 'DELETE'
      });
    }

    findAnonymous(pollId) {

      return $.get(AppConfig.backendServer+'/rest/poll/' + pollId + '/anonymousPoll');
    }

    findAnonymousPoll(pollId) {

      return $.get(AppConfig.backendServer+'/rest/poll/' + pollId + '/anonymous');
    }

    addAnonymousSelection(selection) {

      return $.ajax({
        url: AppConfig.backendServer+'/rest/poll/' + selection.pollId + '/selection/anonymous',
        method: 'POST',
        data: JSON.stringify(selection),
        contentType: 'application/json'
      });
    }

  }
  