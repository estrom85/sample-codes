using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Runtime.Serialization;

namespace SharedData
{
    [DataContract]
    public class ImageFile
    {
        [DataMember]
        public int ImageId { set; get; }

        [DataMember]
        public int POIId { set; get; }

        [DataMember]
        public string Name { set; get; }

        [DataMember]
        public string FileName { set; get; }

        [DataMember]
        public string Description { set; get; }

        [DataMember]
        public long Size { set; get; }

        [DataMember]
        public string Type { set; get; }

        [DataMember]
        public byte[] Data { set; get; }
    }
}
