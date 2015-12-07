using SyncWebService.Exceptions;
using System;
using System.Collections.Generic;
using System.Linq;
using System.Runtime.Serialization;
using System.ServiceModel;
using System.Text;
using SharedData;

namespace SyncWebService
{
    /*
     * Webservice interface
     * 
     * this interface is used for communication with webservice. 
     * Provides basic operation that can be used by 
     */
    [ServiceContract]
    public interface ISyncService
    {
        /*
         * RegisterSession
         * 
         * this function registers session for desktop application instance on the server.
         * For the session to be registered successfuly, client has to provide valid login information
         * if login is sucessful, session is registered and session id is returned to the client.
         * this id is then used for all other methods. 
         * 
         * This method has to be called first.
         * 
         * parameters:
         * userID:string - identification of the user
         * password:string - valid password
         * 
         * returns:
         * Guid - if valid login information are provided, function will return session id for the user
         * 
         * throws:
         * AuthorisationFault exception - this exception is thrown when error during authorisation occurs
         *  e.g. wrong userID or password is provided, connection to database failed, ...
         */
        
        [OperationContract]
        [FaultContract(typeof(AuthorisationFault))]
        Guid RegisterSession(string userID, string password);

        /*
         * DestroySession
         * 
         * destroys session on the server. This method is called when user is logged out
         * of the service.
         * 
         * parameters:
         * sessionID:Guid - valid session id
         */
        [FaultContract(typeof(AuthorisationFault))]
        [OperationContract]
        void DestroySession(Guid sessionID);

        #region GETTERS

        [FaultContract(typeof(AuthorisationFault))]
        [OperationContract]
        UserInfo GetUserInfo(Guid sessionId);

        [FaultContract(typeof(AuthorisationFault))]
        [OperationContract]
        List<UserInfo> GetUserList(Guid sessionId);

        [FaultContract(typeof(AuthorisationFault))]
        [OperationContract]
        List<Tour> GetTours(Guid sessionId);

        [FaultContract(typeof(AuthorisationFault))]
        [OperationContract]
        Tour GetTour(Guid sessionId, int tourId);

        [FaultContract(typeof(AuthorisationFault))]
        [OperationContract]
        List<POI> GetPOIs(Guid sessionId, int tourId);

        [FaultContract(typeof(AuthorisationFault))]
        [OperationContract]
        List<MessageHeader> GetMessages(Guid sessionId);

        [FaultContract(typeof(AuthorisationFault))]
        [OperationContract]
        List<MessageHeader> GetMessagesSent(Guid sessionId);

        [FaultContract(typeof(AuthorisationFault))]
        [OperationContract]
        List<MessageHeader> GetMessagesFrom(Guid sessionId, DateTime from);

        [FaultContract(typeof(AuthorisationFault))]
        [OperationContract]
        Message GetMessage(Guid sessionId, int messageId);

        [FaultContract(typeof(AuthorisationFault))]
        [OperationContract]
        ImageFile GetImage(Guid sessionId, int imageId);

        [FaultContract(typeof(AuthorisationFault))]
        [OperationContract]
        List<int> GetImageIDsForTour(Guid sessionId, int tourId);

        [FaultContract(typeof(AuthorisationFault))]
        [OperationContract]
        List<int> GetImageIDsForPOI(Guid sessionId, int POIId);

        #endregion GETTERS

        #region SETTERS

        [FaultContract(typeof(AuthorisationFault))]
        [OperationContract]
        void UploadImage(Guid sessionId, ImageFile image);

        [FaultContract(typeof(AuthorisationFault))]
        [OperationContract]
        void SendMessage(Guid sessionId, string sendTo, string title, string body);

        #endregion SETTERS
    }
}
