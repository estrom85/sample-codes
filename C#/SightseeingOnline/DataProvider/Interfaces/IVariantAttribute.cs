using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace DataProvider.Interfaces
{
    public interface IVariantAttribute
    {
        int ID { get; }
        string SYSTEM_UUID { get; }
        Nullable<int> FRONTEND_ID { get; }
        string NAME { get; }
        string DESCRIPTION { get; }
        Nullable<System.DateTime> CREATED_AT { get; }
        Nullable<System.DateTime> UPDATED_AT { get; }

        ICollection<IVariantValue> PRODUCTVARIANTATTRIBUTEVALUES { get; }
    }
}
