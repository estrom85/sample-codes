using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Runtime.Serialization;

namespace SharedData
{
    public class POI
    {
        [DataMember]
        public int POIId { set; get; }

        [DataMember]
        public string Name { set; get; }

        [DataMember]
        public decimal Latitude { set; get; }

        [DataMember]
        public decimal Longitude { set; get; }
    }
}
