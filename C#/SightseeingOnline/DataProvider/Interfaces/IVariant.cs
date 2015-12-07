using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace DataProvider.Interfaces
{
    public interface IVariant
    {
        int ID { get; }
        string SYSTEM_UUID { get; }
        Nullable<int> FRONTEND_ID { get; }
        int PRODUCT_ID { get; }
        Nullable<decimal> PRICE { get; }
        Nullable<int> QUANTITY { get; }
        Nullable<System.DateTime> CREATED_AT { get; }
        Nullable<System.DateTime> UPDATED_AT { get; }
        bool PUBLISHED { get; }
        bool DELETED { get; }

        IProduct PRODUCT { get; }
        IVariantValue DATE { get; }
        IVariantValue PACKAGE { get; }
    }
}
