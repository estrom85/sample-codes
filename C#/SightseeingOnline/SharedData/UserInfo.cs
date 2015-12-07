using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Runtime.Serialization;

namespace SharedData
{
    [DataContract]
    public class UserInfo
    {
        [DataMember]
        public int UserID { get; set; }

        [DataMember]
        public string Name { get; set; }

        [DataMember]
        public string Surname { get; set; }

        [DataMember]
        public string Role { get; set; }

        [DataMember]
        public string email { get; set; }
        
    }
}
