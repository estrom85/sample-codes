using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace DataProvider.Interfaces
{
    public interface IPOI
    {
        int ID { get; }
        string SYSTEM_UUID { get; }
        string NAME { get; }
        string DESCRIPTION { get; }
        Nullable<decimal> LONGITUDE { get; }
        Nullable<decimal> LATITUDE { get; }
        Nullable<System.DateTime> CREATED_AT { get; }
        Nullable<System.DateTime> UPDATED_AT { get; }

        ICollection<IProduct> PRODUCTS { get; }
        ICollection<IMedia> MEDIA { get; }
    }
}
