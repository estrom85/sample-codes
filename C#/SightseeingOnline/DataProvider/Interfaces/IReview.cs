using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace DataProvider.Interfaces
{
    public interface IReview
    {
        int ID { get; }
        Nullable<int> PRODUCT_ID { get; }
        Nullable<System.DateTime> TIME { get; }
        string REVIEW { get; }
        Nullable<int> MARK { get; }
        Nullable<System.DateTime> CREATED_AT { get; }
        Nullable<System.DateTime> UPDATED_AT { get; }

        IProduct PRODUCTS { get; }
    }
}
