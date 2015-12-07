using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace SyncWebService
{
    public class Session
    {
        public DateTime TimeStamp { set; get; }
        public int UserID { set; get; }
        public Guid SessionID { set; get; }
    }
}
