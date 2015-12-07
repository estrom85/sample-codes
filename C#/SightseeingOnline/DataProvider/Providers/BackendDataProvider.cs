using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using DataProvider.Interfaces;
using DataProvider.EntityModels.BackendModel;
using DataProvider.DataModel;
using DataProvider.Enumerations;
using DataProvider.DataModel.BackendModel;
using System.Data.Entity;
using System.IO;
using System.Drawing;
using System.Data.Entity.Validation;

namespace DataProvider.Providers
{
    public class BackendDataProvider
    {
        private BackendEntities _backendModel = new BackendEntities();

        public string ImageDirectory { set; get; }

        #region USER_MANAGEMENT

        public IUser GetUser(int id)
        {
            var user = from c in _backendModel.USERS
                       where c.ID == id
                       select c;
            if (user.Count() != 1) return null;
            return new BEUser(user.Single());
        }

        public int Authorize(string email, string psswd)
        {
            var user = from c in _backendModel.USERS
                       where c.EMAIL == email
                       select c;
            if (user.Count() != 1) return -1;

            if (BCrypt.Net.BCrypt.Verify(psswd, user.Single().ENCRYPTED_PASSWORD))
            {
                return user.Single().ID;
            }

            return -1;
        }

        public ICollection<IUser> GetUsers()
        {
            var users = from c in _backendModel.USERS
                        select c;
            var output = new HashSet<IUser>();
            foreach (var u in users)
            {
                output.Add(new BEUser(u));
            }
            return output;
        }

        #endregion USER_MANAGEMENT

        public void Reset()
        {
            try
            {
                _backendModel.SaveChanges();
                _backendModel.Dispose();
                _backendModel = new BackendEntities();
            }
            catch (DbEntityValidationException e)
            {
                int i = 0;
            }
        }
        #region TOURS_MANAGEMENT

        /*
         * Returns product from database based on id
         */
        #region TOUR
        public IProduct GetTour(int id, bool isFrontendID = false)
        {
            var prod = from c in _backendModel.PRODUCTS
                       where isFrontendID ? c.FRONTEND_ID == id : c.ID == id
                       select c;
            if (prod.Count() == 0)
            {
                throw new ArgumentException("ID not found");
            }

            return new BEProduct(prod.Single());
        }

        public ICollection<IProduct> GetTours(DateTime fromDate, Role role, bool includeUnpublished = false)
        {


            IEnumerable<PRODUCTS> products;

            switch (role)
            {
                case Role.NEW:
                    products = from c in _backendModel.PRODUCTS
                               where c.CREATED_AT > fromDate
                               select c;
                    break;
                case Role.CHANGED:
                    products = from c in _backendModel.PRODUCTS
                               where c.UPDATED_AT > fromDate
                               select c;
                    break;
                default:
                    products = from c in _backendModel.PRODUCTS
                               where (c.UPDATED_AT.HasValue ? c.UPDATED_AT > fromDate : c.CREATED_AT > fromDate)
                               select c;
                    break;
            }

            if (!includeUnpublished)
            {
                products = from c in products
                           where c.PUBLISHED != 0 || c.PUBLISHED != null
                           select c;
            }

            ICollection<IProduct> output = new HashSet<IProduct>();
            foreach (PRODUCTS prod in products)
            {
                output.Add(new BEProduct(prod));
            }

            return output;
        }

        public void UpdateReviews(ICollection<IReview> reviews)
        {
            foreach (IReview review in reviews)
            {
                var review_query = from r in _backendModel.PRODUCTREVIEWS
                                   where r.ID == review.ID
                                   select r;
                var product_query = from p in _backendModel.PRODUCTS
                                    where p.FRONTEND_ID == review.PRODUCTS.FRONTEND_ID
                                    select p;

                if (product_query.Count() == 0)
                {
                    continue;
                }

                PRODUCTS product = product_query.Single();

                if (review_query.Count() == 0)
                {
                    PRODUCTREVIEWS rev = new PRODUCTREVIEWS
                    {
                        ID = review.ID,
                        REVIEW = review.REVIEW,
                        MARK = review.MARK,
                        TIME = review.TIME
                    };
                    product.PRODUCTREVIEWS.Add(rev);
                }
                else
                {
                    var rev_query = from r in _backendModel.PRODUCTREVIEWS
                                    where r.ID == review.ID
                                    select r;
                    if (rev_query.Count() == 0)
                    {
                        continue;
                    }

                    var rev = rev_query.Single();

                    rev.MARK = review.MARK;
                    rev.REVIEW = review.REVIEW;
                }

                product.AVERAGE_RATING = (double?)review.PRODUCTS.AVERAGE_RATING;
                product.HITS = review.PRODUCTS.HITS;
            }
            _backendModel.SaveChanges();
        }

        public void UpdateProducts(ICollection<IProduct> products)
        {
            foreach (IProduct prod in products)
            {
                var prod_query = from p in _backendModel.PRODUCTS
                                 where p.FRONTEND_ID == prod.FRONTEND_ID
                                 select p;

                if(prod_query.Count() == 0)
                {
                    continue;
                }

                prod_query.First().HITS = prod.HITS;
            }

            _backendModel.SaveChanges();
        }


        public void AddReview(int id, IReview review, bool isFrontendID = true, decimal? averageRating = null, int hits = 0)
        {
            if (review == null)
            {
                throw new ArgumentNullException();
            }

            PRODUCTS product;

            product = (from c in _backendModel.PRODUCTS
                       where (isFrontendID) ? c.FRONTEND_ID == id : c.ID == id
                       select c).Single();

            if (!_backendModel.PRODUCTS.Contains(product))
            {
                throw new ArgumentException("Product not found");
            }

            PRODUCTREVIEWS rev = new PRODUCTREVIEWS();

            #region CREATE_REVIEW
            rev.CREATED_AT = review.CREATED_AT;
            rev.MARK = review.MARK;
            rev.REVIEW = review.REVIEW;
            rev.TIME = review.TIME;
            rev.UPDATED_AT = review.UPDATED_AT;
            #endregion CREATE_REVIEW;

            product.PRODUCTREVIEWS.Add(rev);

            #region GET_AVG_RATING
            if (averageRating == null)
            {
                int sum = 0;
                int count = 0;
                foreach (PRODUCTREVIEWS r in product.PRODUCTREVIEWS)
                {
                    if (r.MARK != null)
                    {
                        sum += r.MARK.Value;
                        count++;
                    }
                }
                averageRating = (decimal)sum / (decimal)count;
            }
            #endregion GET_AVG_RATING

            product.AVERAGE_RATING = (double?)averageRating;
            product.HITS = hits;

            _backendModel.SaveChanges();
        }

        public ICollection<IReview> GetReviews(DateTime fromDate)
        {
            var reviews = from r in _backendModel.PRODUCTREVIEWS
                          where r.UPDATED_AT > fromDate
                          select r;

            HashSet<IReview> output = new HashSet<IReview>();
            foreach (var rev in reviews)
            {
                output.Add(new BEReview(rev));
            }

            return output;
        }
        
        public void AssignTourFrontendID(int id, int frontendID)
        {
            PRODUCTS prod = (from c in _backendModel.PRODUCTS
                             where c.ID == id
                             select c).Single();
            prod.FRONTEND_ID = frontendID;
            _backendModel.SaveChanges();
        }

        #endregion TOUR

        #region CATEGORY
        public ICategory GetCategory(int id, bool isFrontendID = false)
        {
            var category = from c in _backendModel.PRODUCTCATEGORIES
                           where (isFrontendID) ? c.FRONTEND_ID == id : c.ID == id
                           select c;
            if (category.Count() == 0)
            {
                throw new ArgumentException("ID not found");
            }

            return new BECategory(category.Single());
        }

        public ICollection<ICategory> GetCategories(DateTime fromDate, Role role)
        {
            IEnumerable<PRODUCTCATEGORIES> cat;
            switch (role)
            {
                case Role.NEW:
                    cat = from c in _backendModel.PRODUCTCATEGORIES
                          where c.CREATED_AT > fromDate
                          select c;
                    break;
                case Role.CHANGED:
                    cat = from c in _backendModel.PRODUCTCATEGORIES
                          where c.UPDATED_AT > fromDate
                          select c;
                    break;
                default:
                    cat = from c in _backendModel.PRODUCTCATEGORIES
                          where (c.UPDATED_AT.HasValue ? c.UPDATED_AT > fromDate : c.CREATED_AT > fromDate)
                          select c;
                    break;
            }
            HashSet<ICategory> output = new HashSet<ICategory>();

            foreach (PRODUCTCATEGORIES category in cat)
            {
                output.Add(new BECategory(category));
            }

            return output;
        }

        public void AssignCategoryFrontendID(int id, int frontendID)
        {
            PRODUCTCATEGORIES category = (from c in _backendModel.PRODUCTCATEGORIES
                                          where c.ID == id
                                          select c).Single();
            category.FRONTEND_ID = frontendID;
            _backendModel.SaveChanges();
        }
        #endregion CATEGORY

        #region POI
        public IPOI GetPOI(int id)
        {
            var poi = from c in _backendModel.POIS
                      where c.ID == id
                      select c;
            if (poi.Count() == 0)
            {
                throw new ArgumentException("ID not found");
            }

            return new BEPOI(poi.Single());
        }

        public ICollection<IPOI> GetPOIs(DateTime fromDate, Role role)
        {
            IEnumerable<POIS> cat;
            switch (role)
            {
                case Role.NEW:
                    cat = from c in _backendModel.POIS
                          where c.CREATED_AT > fromDate
                          select c;
                    break;
                case Role.CHANGED:
                    cat = from c in _backendModel.POIS
                          where c.UPDATED_AT > fromDate
                          select c;
                    break;
                default:
                    cat = from c in _backendModel.POIS
                          where (c.UPDATED_AT.HasValue ? c.UPDATED_AT > fromDate : c.CREATED_AT > fromDate)
                          select c;
                    break;
            }
            HashSet<IPOI> output = new HashSet<IPOI>();

            foreach (POIS poi in cat)
            {
                output.Add(new BEPOI(poi));
            }

            return output;
        }

        #endregion POI

        #region VARIANT_VALUES

        public IVariantValue GetVariantValue(int id, bool isFrontendID)
        {
            var variant = from c in _backendModel.PRODUCTVARIANTATTRIBUTEVALUES
                          where (isFrontendID) ? c.FRONTEND_ID == id : c.ID == id
                          select c;
            if (variant.Count() == 0)
            {
                throw new ArgumentException("ID not found");
            }

            return new BEVariantValue(variant.Single());
        }

        public ICollection<IVariantValue> GetVariantValues(DateTime fromDate, Role role)
        {
            IEnumerable<PRODUCTVARIANTATTRIBUTEVALUES> cat;
            switch (role)
            {
                case Role.NEW:
                    cat = from c in _backendModel.PRODUCTVARIANTATTRIBUTEVALUES
                          where c.CREATED_AT > fromDate
                          select c;
                    break;
                case Role.CHANGED:
                    cat = from c in _backendModel.PRODUCTVARIANTATTRIBUTEVALUES
                          where c.UPDATED_AT > fromDate
                          select c;
                    break;
                default:
                    cat = from c in _backendModel.PRODUCTVARIANTATTRIBUTEVALUES
                          where (c.UPDATED_AT.HasValue ? c.UPDATED_AT > fromDate : c.CREATED_AT > fromDate)
                          select c;
                    break;
            }
            HashSet<IVariantValue> output = new HashSet<IVariantValue>();

            foreach (PRODUCTVARIANTATTRIBUTEVALUES value in cat)
            {
                output.Add(new BEVariantValue(value));
            }

            return output;
        }

        public void AssignVariantValueFrontendID(int id, int frontendID)
        {
            PRODUCTVARIANTATTRIBUTEVALUES value = (from c in _backendModel.PRODUCTVARIANTATTRIBUTEVALUES
                                                   where c.ID == id
                                                   select c).Single();
            value.FRONTEND_ID = frontendID;
            _backendModel.SaveChanges();
        }
        #endregion VARIANT_VALUES

        #region VARIANTS

        public IVariant GetVariant(int id, bool isFrontendID)
        {
            var variant = from c in _backendModel.PRODUCTVARIANTS
                          where (isFrontendID) ? c.FRONTEND_ID == id : c.ID == id
                          select c;
            if (variant.Count() == 0)
            {
                throw new ArgumentException("ID not found");
            }

            return new BEVariant(variant.Single());
        }

        public ICollection<IVariant> GetVariants(DateTime fromDate, Role role)
        {
            IEnumerable<PRODUCTVARIANTS> cat;
            switch (role)
            {
                case Role.NEW:
                    cat = from c in _backendModel.PRODUCTVARIANTS
                          where c.CREATED_AT > fromDate
                          select c;
                    break;
                case Role.CHANGED:
                    cat = from c in _backendModel.PRODUCTVARIANTS
                          where c.UPDATED_AT > fromDate
                          select c;
                    break;
                default:
                    cat = from c in _backendModel.PRODUCTVARIANTS
                          where (c.UPDATED_AT.HasValue ? c.UPDATED_AT > fromDate : c.CREATED_AT > fromDate)
                          select c;
                    break;
            }
            HashSet<IVariant> output = new HashSet<IVariant>();

            foreach (PRODUCTVARIANTS variant in cat)
            {
                output.Add(new BEVariant(variant));
            }

            return output;
        }

        public void AssignVariantFrontendID(int id, int frontendID)
        {
            PRODUCTVARIANTS value = (from c in _backendModel.PRODUCTVARIANTS
                                     where c.ID == id
                                     select c).Single();
            value.FRONTEND_ID = frontendID;
            try
            {
                _backendModel.SaveChanges();
            }
            catch (DbEntityValidationException e)
            {
                int i = 0;
            }
        }
        #endregion VARIANTS

        #region MEDIA
        public void AddMedia(IMedia medium, byte[] data = null, bool createThumb = false)
        {
            string path = ImageDirectory == null ? "" : ImageDirectory;
            path += @"\" + medium.PATH.Replace(" ", "_");
            path = path.ToLower();

            if (data == null && !File.Exists(path))
            {
                throw new ArgumentException("You have to provide path to existing file or raw data");
            }

            MEDIA med = new MEDIA
            {
                NAME = medium.NAME,
                DESCRIPTION = medium.DESCRIPTION,
                PATH = medium.PATH.Replace(" ", "_").ToLower(),
                MEDIUMTYPE = medium.MEDIUMTYPE,
                SYSTEM_UUID = Guid.NewGuid().ToString(),
                CREATED_AT = DateTime.Now
            };

            var poi = from c in _backendModel.POIS
                      where c.ID == medium.POIS.ID
                      select c;
            med.POIS = poi.Count() == 1 ? poi.Single() : null;

            _backendModel.MEDIA.Add(med);
            _backendModel.SaveChanges();

            if (data == null) return;

            File.WriteAllBytes(path, data);

            if (!createThumb) return;

            //Image bmp = new Bitmap(path).GetThumbnailImage(50, 50, thumbCallback, IntPtr.Zero);
            string[] split = path.Split('.');
            string tmb_path = "";
            for (int i = 0; i < split.Count() - 1; i++)
            {
                tmb_path += split[i];
                if (i < split.Count() - 2) tmb_path += '.';
            }
            tmb_path += "_thumb." + split.Last();
            Images.ImageHandler.CreateThumb(path, tmb_path, 150, 150);
            //bmp.Save(tmb_path);
        }

        public IMedia GetMedia(int id, bool isFrontendID = false)
        {
            var media = from c in _backendModel.MEDIA
                        where (isFrontendID) ? c.FRONTEND_ID == id : c.ID == id
                        select c;
            if (media.Count() == 0)
            {
                throw new ArgumentException("ID not found");
            }

            return new BEMedia(media.Single());
        }

        public ICollection<IMedia> GetMedia(DateTime fromDate, Role role)
        {
            IEnumerable<MEDIA> med;
            switch (role)
            {
                case Role.NEW:
                    med = from c in _backendModel.MEDIA
                          where c.CREATED_AT > fromDate
                          select c;
                    break;
                case Role.CHANGED:
                    med = from c in _backendModel.MEDIA
                          where c.UPDATED_AT > fromDate
                          select c;
                    break;
                default:
                    med = from c in _backendModel.MEDIA
                          where (c.UPDATED_AT.HasValue ? c.UPDATED_AT > fromDate : c.CREATED_AT > fromDate)
                          select c;
                    break;
            }
            HashSet<IMedia> output = new HashSet<IMedia>();

            foreach (var m in med)
            {
                output.Add(new BEMedia(m));
            }

            return output;
        }

        public byte[] GetMediaRawData(int mediaID)
        {
            string path = GetMediaAbsolutePath(mediaID);

            if (File.Exists(path))
            {
                return File.ReadAllBytes(path);
            }
            return null;
        }

        public string GetMediaAbsolutePath(int mediaId)
        {
            var media = from m in _backendModel.MEDIA
                        where m.ID == mediaId
                        select m;
            if (media.Count() == 0)
            {
                return null;
            }

            var med = media.First();

            string path = ImageDirectory == null ? "" : ImageDirectory;
            path += @"\" + med.PATH;

            return path;
        }

        public string GetThumbAbsolutePath(int mediaId)
        {
            string path = GetMediaAbsolutePath(mediaId);

            string[] split = path.Split('.');
            string tmb_path = "";
            for (int i = 0; i < split.Count() - 1; i++)
            {
                tmb_path += split[i];
                if (i < split.Count() - 2) tmb_path += '.';
            }
            tmb_path += "_thumb." + split.Last();

            return tmb_path;
        }

        #endregion MEDIA
        #endregion TOURS_MANAGEMENT

        #region MESSAGES
        public ICollection<IMessage> GetReceivedMessages(string receipent)
        {
            var messages = from c in _backendModel.MESSAGES
                           where c.RECIPIENT.Contains(receipent) || c.RECIPIENT == "all"
                           select c;
            ICollection<IMessage> output = new HashSet<IMessage>();

            foreach (var msg in messages)
            {
                output.Add(new BEMessage(msg));
            }

            return output;
        }

        public ICollection<IMessage> GetReceivedMessages(string receipent, DateTime fromDate)
        {
            var messages = from c in _backendModel.MESSAGES
                           where (c.RECIPIENT.Contains(receipent) || c.RECIPIENT == "all")
                           && c.LOG_TIMESTAMP > fromDate
                           select c;
            ICollection<IMessage> output = new HashSet<IMessage>();

            foreach (var msg in messages)
            {
                output.Add(new BEMessage(msg));
            }

            return output;
        }

        public ICollection<IMessage> GetSentMessages(string sender)
        {
            var messages = from c in _backendModel.MESSAGES
                           where c.SENDER == sender
                           select c;
            ICollection<IMessage> output = new HashSet<IMessage>();

            foreach (var msg in messages)
            {
                output.Add(new BEMessage(msg));
            }

            return output;
        }

        public IMessage GetMessage(int id)
        {
            var msg = from c in _backendModel.MESSAGES
                      where c.ID == id
                      select c;
            if (msg.Count() != 1)
            {
                return null;
            }
            return new BEMessage(msg.Single());
        }

        public void AddMessage(IMessage message)
        {
            if (message == null)
            {
                throw new ArgumentNullException();
            }

            MESSAGES msg = new MESSAGES
            {
                SYSTEM_UUID = Guid.NewGuid().ToString(),
                SENDER = message.SENDER,
                RECIPIENT = message.RECIPIENT,
                BODY = message.BODY,
                TITLE = message.TITLE,
                LOG_TIMESTAMP = message.LOG_TIMESTAMP,
                CREATED_AT = DateTime.Now
            };

            _backendModel.MESSAGES.Add(msg);
            _backendModel.SaveChanges();
        }

        #endregion MESSAGES

        #region ORDERS_MANAGEMENT
        public void UpdateOrders(ICollection<IOrder> orders, int[] statistics = null)
        {
            if (orders == null)
            {
                throw new ArgumentNullException();
            }

            if (orders.Count == null)
            {
                _setUpdateStatistics(0, 0, statistics);
                return;
            }

            int newRecords = 0;
            foreach (IOrder order in orders)
            {
                //find out if the order is already in the database
                var record = from o in _backendModel.ORDERS
                             where o.FRONTEND_ID == order.FRONTEND_ID
                             select o;

                if (record.Count() > 0)
                {
                    //ignore already added orders
                    continue;
                }

                ORDERS new_order = new ORDERS
                {
                    SYSTEM_UUID = Guid.NewGuid().ToString(),
                    FRONTEND_ID = order.FRONTEND_ID,
                    NUMBER = order.NUMBER,
                    TOTAL = order.TOTAL,
                    STATUS = order.STATUS,
                    ORDER_DATE = order.ORDER_DATE,
                    FIRSTNAME = order.FIRSTNAME,
                    LASTNAME = order.LASTNAME,
                    COMPANY = order.COMPANY,
                    EMAIL = order.EMAIL,
                    STREET = order.STREET,
                    ZIP = order.ZIP,
                    CITY = order.CITY,
                    STATE = order.STATE,
                    COUNTRY = order.COUNTRY,
                    PHONE = order.PHONE,
                    HASHCODE = order.HASHCODE,
                    CREATED_AT = order.CREATED_AT
                };

                new_order.CUSTOMERS = GetCustomer(order.CUSTOMER);

                foreach (IOrderItem item in order.ORDERITEMS)
                {
                    //get product 
                    var product = from p in _backendModel.PRODUCTS
                                  where p.FRONTEND_ID == item.PRODUCT.FRONTEND_ID
                                  select p;
                    if (product.Count() != 1)
                    {
                        continue;
                    }

                    ORDERITEMS order_item = new ORDERITEMS
                    {
                        SYS_UUID = Guid.NewGuid().ToString(),
                        FRONTEND_ID = item.FRONTEND_ID,
                        PRODUCT_QUANTITY = item.PRODUCT_QUANTITY,
                        PRODUCT_ITEM_PRICE = item.PRODUCT_ITEM_PRICE,
                        PRODUCT_TAX = item.PRODUCT_TAX,
                        PRODUCT_ATTRIBUTES = item.PRODUCT_ATTRIBUTES,
                        CREATED_AT = item.CREATED_AT
                    };
                    order_item.PRODUCTS = product.Single();
                    new_order.ORDERITEMS.Add(order_item);

                    _refreshQuantities(item.PRODUCT);
                }
                _backendModel.ORDERS.Add(new_order);
                newRecords++;
            }
            _backendModel.SaveChanges();
            _setUpdateStatistics(newRecords, 0, statistics);
        }

        private void _refreshQuantities(IProduct product)
        {
            var prod_query = from p in _backendModel.PRODUCTS
                             where p.FRONTEND_ID == product.FRONTEND_ID
                             select p;
            if (prod_query.Count() == 0)
            {
                return;
            }

            var prod = prod_query.First();
            prod.QUANTITY = product.QUANTITY;

            foreach (var variant in product.VARIANTS)
            {
                var var_query = from v in _backendModel.PRODUCTVARIANTS
                                where v.FRONTEND_ID == variant.FRONTEND_ID
                                select v;
                if (var_query.Count() == 0)
                {
                    continue;
                }
                var var_element = var_query.First();
                var_element.QUANTITY = variant.QUANTITY;
            }
            _backendModel.SaveChanges();
        }

        private CUSTOMERS GetCustomer(ICustomer customer)
        {
            var cust_query = from c in _backendModel.CUSTOMERS
                             where c.FRONTEND_ID == customer.FRONTEND_ID
                             select c;

            if (cust_query.Count() == 0)
            {
                CUSTOMERS cust = new CUSTOMERS
                {
                    SYSTEM_UUID = Guid.NewGuid().ToString(),
                    FRONTEND_ID = customer.FRONTEND_ID,
                    USERNAME = customer.USERNAME,
                    FIRSTNAME = customer.FIRSTNAME,
                    LASTNAME = customer.LASTNAME,
                    COMPANY = customer.COMPANY,
                    EMAIL = customer.EMAIL,
                    STREET = customer.STREET,
                    ZIP = customer.ZIP,
                    CITY = customer.CITY,
                    STATE = customer.STATE,
                    COUNTRY = customer.COUNTRY,
                    PHONE = customer.PHONE,
                    CREATED_AT = customer.CREATED_AT
                };
                _backendModel.CUSTOMERS.Add(cust);
                _backendModel.SaveChanges();
                return cust;
            }
            return cust_query.First();
        }

        public void UpdateCustomers(ICollection<ICustomer> customers, int[] statistics = null)
        {
            if (customers == null)
            {
                throw new ArgumentNullException();
            }

            if (customers.Count == 0)
            {
                _setUpdateStatistics(0, 0, statistics);
                return;
            }

            int count = 0;
            foreach (ICustomer cust in customers)
            {
                var cust_query = from c in _backendModel.CUSTOMERS
                                 where c.FRONTEND_ID == cust.FRONTEND_ID
                                 select c;
                if (cust_query.Count() == 0)
                {
                    continue;
                }

                var customer = cust_query.First();

                customer.FIRSTNAME = cust.FIRSTNAME;
                customer.LASTNAME = cust.LASTNAME;
                customer.COMPANY = cust.COMPANY;
                customer.EMAIL = cust.EMAIL;
                customer.STREET = cust.STREET;
                customer.ZIP = cust.ZIP;
                customer.CITY = cust.CITY;
                customer.STATE = cust.STATE;
                customer.COUNTRY = cust.COUNTRY;
                customer.PHONE = cust.PHONE;

                count++;
            }
            _backendModel.SaveChanges();
            _setUpdateStatistics(0, count, statistics);
        }

        #endregion ORDERS_MANAGEMENT

        private void _setUpdateStatistics(int newRecords, int updatedRecords, int[] statistics)
        {
            if (statistics == null) return;

            int n = statistics.Count();

            if (n > 0)
            {
                statistics[0] = newRecords + updatedRecords;
                if (n > 1)
                {
                    statistics[1] = newRecords;
                    if (n > 2)
                    {
                        statistics[2] = updatedRecords;
                    }
                }
            }
        }
    }
}
