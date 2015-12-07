using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Runtime.Serialization;

namespace SharedData
{
    [DataContract]
    public class Tour
    {
        [DataMember]
        public int TourID { set; get; }

        [DataMember]
        public string Name { set; get; }

        [DataMember]
        public string Description { set; get; }

        [DataMember]
        public decimal Rating { set; get; }
    }
}
