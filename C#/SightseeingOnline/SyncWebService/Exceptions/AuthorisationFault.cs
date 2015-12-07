using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Runtime.Serialization;

namespace SyncWebService.Exceptions
{
    [DataContract]
    public class AuthorisationFault
    {
        public static int INVALID_LOGIN = 0;
        public static int SESSION_TIMEOUT = 1;
        public static int INVALID_SESSION_ID = 3;
        public static int CONNECTION_ERROR = 4;
        public static int UNKNOWN_ERROR = 5;

        [DataMember]
        public int Type { set; get; }

        [DataMember]
        public string Message { set; get; }
    }
}
