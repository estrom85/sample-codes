using System;
using System.Collections.Generic;
using System.Linq;
using System.Runtime.Serialization;
using System.ServiceModel;
using System.Text;
using DataProvider.Providers;
using SyncWebService.Exceptions;
using SharedData;
using DataProvider.Interfaces;
using SyncWebService.DataModel;
using Logging;

namespace SyncWebService
{
    // NOTE: You can use the "Rename" command on the "Refactor" menu to change the class name "ISyncService" in both code and config file together.
    [ServiceBehavior(AddressFilterMode=AddressFilterMode.Any)]
    public class SyncService : ISyncService
    {
        public static BackendDataProvider DataProvider { set; get; }
        public static Dictionary<Guid,Session> Sessions { set; get; }
        public static int SessionTimeout { set; get; }
        public static ILogger Logger { set; get; }

        public Guid RegisterSession(string userID, string password)
        {
            if (DataProvider == null)
            {
                Logger.WriteMessage(DateTime.Now, "Webservice", "Register session failed: Webservice initialization error");
                throw new FaultException<AuthorisationFault>(
                    new AuthorisationFault
                    {
                        Type = AuthorisationFault.UNKNOWN_ERROR,
                        Message = "Webservice initialization failed"
                    }
                    );
            }
            
            int id = DataProvider.Authorize(userID, password);
            if (id < 0)
            {
                Logger.WriteMessage(DateTime.Now, "Webservice","Register session failed: Invalid login or password");
                throw new FaultException<AuthorisationFault>(
                    new AuthorisationFault
                    {
                        Type = AuthorisationFault.INVALID_LOGIN,
                        Message = "Invalid login or password"
                    }
                    );
            }
            Session ses = new Session { SessionID = Guid.NewGuid(), TimeStamp = DateTime.Now, UserID = id };
            Sessions.Add(ses.SessionID, ses);
            WriteMessage("Session registered", ses);
            //Logger.WriteMessage(DateTime.Now, "Webservice","Session registered. Session id: " + ses.SessionID);
            return ses.SessionID;
        }

        public void DestroySession(Guid sessionID)
        {
            Sessions.Remove(sessionID);
            Logger.WriteMessage(DateTime.Now, "Webservice","Session destroyed. Session id: " + sessionID);
        }

        public UserInfo GetUserInfo(Guid sessionId)
        {
            Session ses = ValidateSession(sessionId);
            IUser user = DataProvider.GetUser(ses.UserID);
            WriteMessage("User info sent", ses);
            //Logger.WriteMessage(DateTime.Now, "Webservice","User info sent. Session id: " + ses.SessionID);

            return new UserInfo 
            {
                UserID = user.ID,
                Name = user.FIRSTNAME,
                Surname = user.LASTNAME,
                email = user.EMAIL
            };    
        }

        public List<UserInfo> GetUserList(Guid sessionId)
        {
            Session ses = ValidateSession(sessionId);

            WriteMessage("User list sent", ses);
            //Logger.WriteMessage(DateTime.Now, "Webservice","User list sent. Session id: " + ses.SessionID);

            return new List<UserInfo>(from c in DataProvider.GetUsers()
                                      select new UserInfo
                                      {
                                          UserID = c.ID,
                                          Name = c.FIRSTNAME,
                                          Surname = c.LASTNAME,
                                          email = c.EMAIL
                                      });
        }

        public List<Tour> GetTours(Guid sessionId)
        {
            Session ses = ValidateSession(sessionId);

            WriteMessage("Tour list sent", ses);
            //Logger.WriteMessage(DateTime.Now, "Webservice","Tour list sent. Session id: " + ses.SessionID);

            return new List<Tour>(from c in DataProvider.GetTours(DateTime.MinValue, global::DataProvider.Enumerations.Role.NEW_OR_CHANGED)
                                  select new Tour
                                  {
                                      TourID = c.ID,
                                      Name = c.NAME,
                                      Description = c.DESCRIPTION,
                                      Rating = c.AVERAGE_RATING.HasValue?c.AVERAGE_RATING.Value:0
                                  });
        }   
        
        public SharedData.Tour GetTour(Guid sessionId, int tourId)
        {
            Session ses = ValidateSession(sessionId);

            IProduct tour = DataProvider.GetTour(tourId);
            WriteMessage("User info sent", ses);
            //Logger.WriteMessage(DateTime.Now, "Webservice","Tour info sent. Session id: " + ses.SessionID);

            return new Tour 
            {
                TourID = tour.ID,
                Name = tour.NAME,
                Description = tour.DESCRIPTION,
                Rating = tour.AVERAGE_RATING.HasValue ? tour.AVERAGE_RATING.Value : 0
            };
        }

        public List<SharedData.POI> GetPOIs(Guid sessionId, int tourId)
        {
            Session ses = ValidateSession(sessionId);

            IProduct tour = DataProvider.GetTour(tourId);

            WriteMessage("POI list sent", ses);
            //Logger.WriteMessage(DateTime.Now, "Webservice","POI list sent. Session id: " + ses.SessionID);

            return new List<POI>(from poi in tour.POIS
                                 select
                                     new POI
                                     {
                                         Name = poi.NAME,
                                         POIId = poi.ID,
                                         Latitude = poi.LATITUDE.HasValue?poi.LATITUDE.Value:0,
                                         Longitude = poi.LONGITUDE.HasValue?poi.LONGITUDE.Value:0
                                     });
        }

        public List<MessageHeader> GetMessages(Guid sessionId)
        {
            Session ses = ValidateSession(sessionId);
            IUser user = DataProvider.GetUser(ses.UserID);

            WriteMessage("Message list sent", ses);
            //Logger.WriteMessage(DateTime.Now, "Webservice", "Message list sent. Session id: " + ses.SessionID);

            return new List<MessageHeader>(from msg in DataProvider.GetReceivedMessages(user.EMAIL)
                                           select new MessageHeader
                                           {
                                               ID = msg.ID,
                                               Reciepents = msg.RECIPIENT,
                                               Sender = msg.SENDER,
                                               Title = msg.TITLE,
                                               Time = msg.LOG_TIMESTAMP
                                           });
        }

        public List<MessageHeader> GetMessagesFrom(Guid sessionId, DateTime fromDate)
        {
            Session ses = ValidateSession(sessionId);

            WriteMessage("Messages list sent", ses);
            //Logger.WriteMessage(DateTime.Now, "Webservice", "Messages list sent. Session id: " + ses.SessionID);

            IUser user = DataProvider.GetUser(ses.UserID);
            return new List<MessageHeader>(from msg in DataProvider.GetReceivedMessages(user.EMAIL,fromDate)
                                           select new MessageHeader
                                           {
                                               ID = msg.ID,
                                               Reciepents = msg.RECIPIENT,
                                               Sender = msg.SENDER,
                                               Title = msg.TITLE,
                                               Time = msg.LOG_TIMESTAMP
                                           });
        }

        public List<MessageHeader> GetMessagesSent(Guid sessionId)
        {
            Session ses = ValidateSession(sessionId);

            IUser user = DataProvider.GetUser(ses.UserID);

            WriteMessage("Sent messages list sent", ses);
            //Logger.WriteMessage(DateTime.Now, "Webservice", "Sent messages list sent. Session id: " + ses.SessionID);

            return new List<MessageHeader>(from msg in DataProvider.GetSentMessages(user.EMAIL)
                                           select new MessageHeader
                                           {
                                               ID = msg.ID,
                                               Reciepents = msg.RECIPIENT,
                                               Sender = msg.SENDER,
                                               Title = msg.TITLE,
                                               Time = msg.LOG_TIMESTAMP
                                           });

            return new List<MessageHeader>();
        }

        public Message GetMessage(Guid sessionId, int messageId)
        {
            Session ses = ValidateSession(sessionId);

            IMessage msg = DataProvider.GetMessage(messageId);

            WriteMessage("Message details sent", ses);
            //Logger.WriteMessage(DateTime.Now, "Webservice", "Message details sent. Session id: " + ses.SessionID);

            if(msg == null) return null;

            return new Message
            {
                Header = new MessageHeader
                {
                    ID = msg.ID,
                    Reciepents = msg.RECIPIENT,
                    Sender = msg.SENDER,
                    Title = msg.TITLE,
                    Time = msg.LOG_TIMESTAMP
                },
                Body = msg.BODY
            };
        }

        public ImageFile GetImage(Guid sessionId, int imageId)
        {
            Session ses = ValidateSession(sessionId);

            IMedia img = DataProvider.GetMedia(imageId);
            byte[] rawData = DataProvider.GetMediaRawData(imageId);

            ImageFile file = new ImageFile
            {
                ImageId = img.ID,
                POIId = img.POIS.ID,
                Name = img.NAME,
                Description = img.DESCRIPTION,
                Data = rawData,
                Size = rawData.Count(),
                Type = img.PATH.Substring(img.PATH.LastIndexOf('.')).ToUpper()
            };

            WriteMessage("Image sent", ses);
            //Logger.WriteMessage(DateTime.Now, "Webservice", "Image sent. Session id: " + ses.SessionID);

            return file;
        }

        public List<int> GetImageIDsForTour(Guid sessionId, int tourId)
        {
            Session ses = ValidateSession(sessionId);

            IProduct tour = DataProvider.GetTour(tourId);
            List<int> output = new List<int>();

            foreach (var poi in tour.POIS)
            {
                foreach (var c in poi.MEDIA)
                {
                    if(c.PUBLISHED)
                        output.Add(c.ID);
                }
            }

            WriteMessage("List of images sent", ses);
            //Logger.WriteMessage(DateTime.Now, "Webservice", "List of images sent. Session id: " + ses.SessionID);

            return output;
        }

        public List<int> GetImageIDsForPOI(Guid sessionId, int POIId)
        {
            Session ses = ValidateSession(sessionId);

            IPOI poi = DataProvider.GetPOI(POIId);

            WriteMessage("List of images sent", ses);
            //Logger.WriteMessage(DateTime.Now,"Webservice", "List of images sent. Session id: " + ses.SessionID);

            List<int> output = new List<int>(from c in poi.MEDIA select c.ID);

            return output;
        }

        public void SendMessage(Guid sessionId, string receipent, string title, string body)
        {
            Session ses = ValidateSession(sessionId);
            IUser user = DataProvider.GetUser(ses.UserID);

            WriteMessage("Message sent", ses);
            //Logger.WriteMessage(DateTime.Now,"Webservice", "Message received. Session id: " + ses.SessionID);

            Message msg = new Message
            {
                Body = body,
                Header = new MessageHeader
                {
                    Sender = user.EMAIL,
                    Reciepents = receipent,
                    Title = title,
                    Time = DateTime.Now
                }
            };
           
            DataProvider.AddMessage(new WSMessage(msg));
        }

        public void UploadImage(Guid sessionId, ImageFile image)
        {

            Session ses = ValidateSession(sessionId);

            WriteMessage("Image received", ses);
            //Logger.WriteMessage(DateTime.Now, "Webservice", "Message received. Session id: " + ses.SessionID);

            DataProvider.AddMedia(new WSMedia(image, DataProvider), image.Data, true);
        }

        private void WriteMessage(string message, Session session)
        {
            string msg = message+"(user: "+session.UserID+"; session: "+session.SessionID+")";
            Logger.WriteMessage(DateTime.Now, "Webservice", msg);
        }

        private Session ValidateSession(Guid sessID)
        {
            Session session;
            if (!Sessions.TryGetValue(sessID, out session))
            {
                Logger.WriteMessage(DateTime.Now, "Webservice", "Session validation failed.: Invalid session id");

                throw new FaultException<AuthorisationFault>(
                    new AuthorisationFault
                    {
                        Type = AuthorisationFault.INVALID_SESSION_ID,
                        Message = "Invalid session"
                    });
            }
            if (session.TimeStamp.AddMilliseconds(SessionTimeout) < DateTime.Now)
            {
                Logger.WriteMessage(DateTime.Now, "Webservice", "Session validation failed: Session exipred");

                Sessions.Remove(sessID);
                throw new FaultException<AuthorisationFault>(
                    new AuthorisationFault
                    {
                        Type = AuthorisationFault.SESSION_TIMEOUT,
                        Message = "Session expired"
                    });
            }
            session.TimeStamp = DateTime.Now;
            return session;
        }
    }
}
