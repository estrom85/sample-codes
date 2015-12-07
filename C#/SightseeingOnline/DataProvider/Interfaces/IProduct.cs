/*
 * Wrapper interface for product tables, Describes common interface for retreiving
 * data from database. 
 * Represents read-only interface for reading database data in the rest 
 * of the application
 */

using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace DataProvider.Interfaces
{
    public interface IProduct
    {
        int ID { get;}
        string SYSTEM_UUID { get;}
        Nullable<int> FRONTEND_ID { get;}
        string NAME { get;}
        Nullable<int> QUANTITY { get;}
        Nullable<decimal> PRICE { get;}
        Nullable<decimal> MIN_PRICE { get;}
        bool PUBLISHED { get;}
        Nullable<int> MEDIUM_ID { get;}
        Nullable<decimal> AVERAGE_RATING { get;}
        Nullable<int> HITS { get; }
        string SHORT_DESCRIPTION { get; }
        string DESCRIPTION { get; }
        Nullable<System.DateTime> CREATED_AT { get; }
        Nullable<System.DateTime> UPDATED_AT { get; }
        bool DELETED { get; }

        ICollection<ICategory> CATEGORIES { get; }
        ICollection<IVariant> VARIANTS { get; }
        ICollection<IReview> REVIEWS { get; }
        ICollection<IPOI> POIS { get; }

        IMedia MEDIA { get; }
    }
}
