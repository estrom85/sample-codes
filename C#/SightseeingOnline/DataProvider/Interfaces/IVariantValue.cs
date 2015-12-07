using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace DataProvider.Interfaces
{
    public interface IVariantValue
    {
        int ID { get; }
        string SYSTEM_UUID { get; }
        Nullable<int> FRONTEND_ID { get; }
        string NAME { get; }
        Nullable<int> PRODUCTVARIANTATTRIBUTE_ID { get; }
        Nullable<System.DateTime> CREATED_AT { get; }
        Nullable<System.DateTime> UPDATED_AT { get; }
        bool DELETED { get; }
        bool PUBLISHED { get; }

        ICollection<IVariant> VARIANTS {get;}
        IVariantAttribute ATTRIBUTE { get; }
    }
}
