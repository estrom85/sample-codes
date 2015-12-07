using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using DataProvider.Interfaces;
using DataProvider.EntityModels.FrontendModel;
using DataProvider.Enumerations;
using System.Data.Entity.Validation;
using System.IO;
using Images;
using HtmlAgilityPack;
using DataProvider.DataModel.FrontendModel;
using System.Diagnostics;

namespace DataProvider.Providers
{
    /*
     * This clas provides communication with frontend database
     * Consists of operations that could be done on database and
     * provides necessary data for further processing.
     * 
     * This is the only access point to the database and only this
     * class is capable of reading from and writing to the frontend
     * database. 
     * 
     * Data provided by the class are read-only.
     */
    public class FrontendDataProvider
    {
        public string ImageDirectory { set; get; }      //Directory for reading and writing image files

        #region UPDATE

        /* Updates all category related tables in the frontend database
         * Method checks if the category already exists in the database. If category
         * is not found, creates new category, in other case updates category data.
         * This method refreshes also frontends main menu and adds or edits the menu items.
         * 
         * Parameters:
         *      categories: ICollection<ICategory> - source of categories data. List of
         *                                           categories, that will be updated
         *      addRecordInformer: Action<int,int> - function callback, this method is called
         *                                           when new category is added. Passes initial
         *                                           id of category and newly assigned id of category
         *                                           in the frontend as parameters to the function
         *      statistics: int[] - reference to array, where function statistics will be stored.
         *                          number of statistics will depend on length of array.
         *                          0th index - number of affected records
         *                          1st index - number of added categories
         *                          2nd index - number of updated categories                                 
         */
        public void UpdateCategories(ICollection<ICategory> categories, Action<int, int> addRecordInformer, int[] statistics = null)
        {
            //initial conditions
            if (categories == null || addRecordInformer == null)
            {
                throw new ArgumentNullException();
            }

            //check if there is need to change anything
            if (categories.Count == 0)
            {
                //enter function statistics
                _setUpdateStatistics(0, 0, statistics);
                return;
            }

            bool isNew = false;
            int newRecords = 0;
            int updatedRecords = 0;

            foreach (ICategory cat in categories)
            {
                CATEGORIES category = null;
                isNew = false;
                //check if category already exists
                if (cat.FRONTEND_ID != null && cat.FRONTEND_ID > 0)
                {
                    //select category
                    var query = from c in _frontendModel.CATEGORIES
                                where c.category_id == cat.FRONTEND_ID
                                select c;
                    if (query.Count() == 1)
                    {
                        category = query.Single();
                    }
                }
                //if category does not exist, create new category
                if (category == null)
                {
                    category = new CATEGORIES();

                    #region MAP_NEW_CATEGORY
                    category.category_parent_id = 0;
                    category.category_ordertype = true;
                    var ord_query = from c in _frontendModel.CATEGORIES
                                    select c.ordering;

                    category.ordering = ord_query.Count() == 0 ? 1 : ord_query.Max() + 1;
                    category.category_add_date = DateTime.Now;
                    category.products_page = 12;
                    category.products_row = 3;
                    category.access = 1;
                    category.alias_de_DE = "";
                    category.short_description_de_DE = "";
                    category.description_de_DE = "";
                    category.meta_title_de_DE = "";
                    category.meta_description_de_DE = "";
                    category.meta_keyword_de_DE = "";
                    category.name_en_GB = "";
                    category.alias_en_GB = "";
                    category.short_description_en_GB = "";
                    category.description_en_GB = "";
                    category.meta_title_en_GB = "";
                    category.meta_description_en_GB = "";
                    category.meta_keyword_en_GB = "";
                    #endregion MAP_NEW_CATEGORY

                    _frontendModel.CATEGORIES.Add(category);
                    isNew = true;
                }

                #region MAP_CATEGORY
                category.name_de_DE = cat.NAME;
                category.category_publish = cat.DELETED ? false : cat.PUBLISHED;
                #endregion MAP_CATEGORY

                _frontendModel.SaveChanges();

                if (isNew)
                {
                    //inform about adding new category
                    addRecordInformer(cat.ID, category.category_id);
                    newRecords++;
                }
                else
                {
                    updatedRecords++;
                }
                //refresh list of products that are connected with this category
                RefreshProductList(cat, category);
            }
            //updates main menu
            UpdateMainMenu(categories);
            //updates statistics
            _setUpdateStatistics(newRecords, updatedRecords, statistics);
        }

        /* Updates all variant values in the frontend database
         * Method checks if the value already exists in the database. If value
         * is not found, creates new valuu, in other case updates value data.
         * This method refreshes also all references to the value in the related tables.
         * 
         * Parameters:
         *      variantValues: ICollection<IVariantValue> - source of values data. List of
         *                                           values, that will be updated
         *      addRecordInformer: Action<int,int> - function callback, this method is called
         *                                           when new value is added. Passes initial
         *                                           id of value and newly assigned id of value
         *                                           in the frontend as parameters to the function
         *      statistics: int[] - reference to array, where function statistics will be stored.
         *                          number of statistics will depend on length of array.
         *                          0th index - number of affected records
         *                          1st index - number of added values
         *                          2nd index - number of updated values                                
         */
        public void UpdateVariantValues(ICollection<IVariantValue> variantValues, Action<int, int> addRecordInformer, int[] statistics = null)
        {
            if (variantValues == null || addRecordInformer == null)
            {
                throw new ArgumentNullException();
            }

            if (variantValues.Count == 0)
            {
                _setUpdateStatistics(0, 0, statistics);
                return;
            }
            int newRecords = 0;
            int updatedRecords = 0;

            foreach (IVariantValue value in variantValues)
            {
                ATTR_VALUES attr_value;
                if (value.FRONTEND_ID == null || value.FRONTEND_ID == 0)
                {
                    attr_value = new ATTR_VALUES();
                    int? attr_id = value.ATTRIBUTE.FRONTEND_ID;
                    attr_value.attr_id = (!attr_id.HasValue) ? 0 : attr_id.Value;
                    var ord_query = from c in _frontendModel.ATTR_VALUES
                                    where c.attr_id == attr_value.attr_id
                                    select c.value_ordering;
                    attr_value.value_ordering = ord_query.Count() == 0 ? 1 : ord_query.Max() + 1;
                    attr_value.image = "";
                    attr_value.name_de_DE = value.NAME;
                    attr_value.name_en_GB = value.NAME;
                    _frontendModel.ATTR_VALUES.Add(attr_value);
                    _frontendModel.SaveChanges();

                    addRecordInformer(value.ID, attr_value.value_id);
                    newRecords++;
                }
                else
                {
                    attr_value = (from c in _frontendModel.ATTR_VALUES
                                  where c.value_id == value.FRONTEND_ID
                                  select c).Single();
                    attr_value.name_de_DE = value.NAME;
                    attr_value.name_en_GB = value.NAME;
                    _frontendModel.SaveChanges();
                    updatedRecords++;
                }
            }
            _setUpdateStatistics(newRecords, updatedRecords, statistics);
        }

        /* Updates all products in the frontend database
         * Method checks if the product already exists in the database. If product
         * is not found, creates new product, in other case updates product data.
         * This method refreshes also all references to the value in the related tables.
         * 
         * Parameters:
         *      tours: ICollection<IProduct> - source of values data. List of
         *                                           products, that will be updated
         *      addRecordInformer: Action<int,int> - function callback, this method is called
         *                                           when new product is added. Passes initial
         *                                           id of product and newly assigned id of product
         *                                           in the frontend as parameters to the function
         *      statistics: int[] - reference to array, where function statistics will be stored.
         *                          number of statistics will depend on length of array.
         *                          0th index - number of affected records
         *                          1st index - number of added records
         *                          2nd index - number of updated records                                
         */
        public void UpdateTours(ICollection<IProduct> tours, Action<int, int> addRecordInformer, int[] statistics = null)
        {
            if (tours == null || addRecordInformer == null)
            {
                throw new ArgumentNullException();
            }

            if (tours.Count == 0)
            {
                _setUpdateStatistics(0, 0, statistics);
                return;
            }

            int newRecords = 0;
            int updatedRecords = 0;

            foreach (IProduct tour in tours)
            {
                if (tour.FRONTEND_ID == null)
                {
                    AddTour(tour, addRecordInformer);
                    newRecords++;
                }
                else
                {
                    EditTour(tour);
                    updatedRecords++;
                }
            }
            _setUpdateStatistics(newRecords, updatedRecords, statistics);
        }

        /* Updates all variants in the frontend database
         * Method checks if the product already exists in the database. If variant
         * is not found, creates new variant, in other case updates product data.
         * This method refreshes also all references to the variant in the related tables.
         * 
         * Parameters:
         *      variants: ICollection<IVariant> - source of values data. List of
         *                                           products, that will be updated
         *      addRecordInformer: Action<int,int> - function callback, this method is called
         *                                           when new variant is added. Passes initial
         *                                           id of variant and newly assigned id of variant
         *                                           in the frontend as parameters to the function
         *      statistics: int[] - reference to array, where function statistics will be stored.
         *                          number of statistics will depend on length of array.
         *                          0th index - number of affected records
         *                          1st index - number of added records
         *                          2nd index - number of updated records                                
         */
        public void UpdateVariants(ICollection<IVariant> variants, Action<int, int> addRecordInformer, int[] statistics = null)
        {
            if (variants == null || addRecordInformer == null)
            {
                throw new ArgumentNullException();
            }

            if (variants.Count == 0)
            {
                _setUpdateStatistics(0, 0, statistics);
                return;
            }

            int newRecord = 0;
            int updatedRecord = 0;
            int removedRecords = 0;

            foreach (IVariant variant in variants)
            {
                if (variant.FRONTEND_ID.HasValue)
                {
                    PRODUCT_ATTRIBUTES attr = (from c in _frontendModel.PRODUCT_ATTRIBUTES
                                               where c.product_attr_id == variant.FRONTEND_ID
                                               select c).Single();

                    if (!variant.PUBLISHED || variant.DELETED)
                    {
                        _frontendModel.PRODUCT_ATTRIBUTES.Remove(attr);
                        removedRecords++;
                        _frontendModel.SaveChanges();
                        continue;
                    }

                    attr.price = getValue(variant.PRICE);
                    attr.count = getValue(variant.QUANTITY);
                    attr.attr_4 = getValue(variant.PACKAGE.FRONTEND_ID);
                    attr.attr_5 = getValue(variant.DATE.FRONTEND_ID);
                   
                    _frontendModel.SaveChanges();
                    updatedRecord++;
                }
                else
                {
                    if (!variant.PUBLISHED || variant.DELETED)
                    {
                        continue;
                    }
                    PRODUCT_ATTRIBUTES attr = new PRODUCT_ATTRIBUTES();
                    attr.product_id = getValue(variant.PRODUCT.FRONTEND_ID);
                    attr.buy_price = 0;
                    attr.price = getValue(variant.PRICE);
                    attr.old_price = 0;
                    attr.count = getValue(variant.QUANTITY);
                    attr.ean = "";
                    attr.weight = 0;
                    attr.weight_volume_units = 0;
                    attr.ext_attribute_product_id = 0;
                    attr.attr_4 = getValue(variant.PACKAGE.FRONTEND_ID);
                    attr.attr_5 = getValue(variant.DATE.FRONTEND_ID);
                   
                    _frontendModel.PRODUCT_ATTRIBUTES.Add(attr);
                    _frontendModel.SaveChanges();
                    addRecordInformer(variant.ID, attr.product_attr_id);

                    newRecord++;
                }
            }
            _setUpdateStatistics(newRecord, updatedRecord, statistics);
            if (statistics.Count() > 0)
            {
                statistics[0] += removedRecords;
                if (statistics.Count() > 3)
                {
                    statistics[3] = removedRecords;
                }
            }
        }

        /* Updates all media in the frontend database
         * Method checks if the medium already exists in the database. If medium
         * is not found, creates new medium, in other case updates medium data.
         * This method refreshes also all references to the value in the related tables.
         * This method peforms file transfer when necessary
         * 
         * Parameters:
         *      media: ICollection<IMedia> - source of values data. List of
         *                                           media, that will be updated
         *     getRawData: Func<int, byte[]> - callback function to retrieve raw file data of
         *                                     medium. Passes id of medium and returns raw data.
         *      statistics: int[] - reference to array, where function statistics will be stored.
         *                          number of statistics will depend on length of array.
         *                          0th index - number of affected records
         *                          1st index - number of added records
         *                          2nd index - number of updated records                                
         */
        public void UpdateMedia(ICollection<IMedia> media, Func<int, byte[]> getRawData, int[] statistics = null)
        {
            if (media == null || getRawData == null)
            {
                throw new ArgumentNullException();
            }

            if (media.Count == 0)
            {
                _setUpdateStatistics(0, 0, statistics);
                return;
            }

            int updatedRecords = 0;
            int removedRecords = 0;

            string path;
            byte[] rawData;
            foreach (IMedia m in media)
            {
                updateMediaReference(m);
                path = ImageDirectory + @"\";
                /*
                
                */
                if (!m.PUBLISHED)
                {
                    if (File.Exists(path))
                    {
                        File.Delete(path);
                        File.Delete("full_" + path);
                        File.Delete("thumb_" + path);
                    }
                    removedRecords++;
                    continue;
                }
                if (m.MEDIUMTYPE != "IMG")
                {
                    continue;
                }
                rawData = getRawData(m.ID);
                try
                {
                    File.WriteAllBytes(path + "full_" + m.PATH, rawData);
                    ImageHandler.CreateThumb(path + "full_" + m.PATH, path + m.PATH, 200, 200);
                    ImageHandler.CreateThumb(path + m.PATH, path + "thumb_" + m.PATH, 100, 100);
                }
                catch (Exception) { }
                updatedRecords++;
            }
            _setUpdateStatistics(0, updatedRecords, statistics);
            if (statistics.Count() > 0)
            {
                statistics[0] += removedRecords;
                if (statistics.Count() > 3)
                {
                    statistics[3] = removedRecords;
                }
            }
        }

        /* Updates all points of interests in the frontend database
         * Method updates product description and refreshes or adds pois into the description
         * 
         * Parameters:
         *      pois: ICollection<IPOI> - source of POI data. List of
         *                                           pois, that will be updated                            
         */
        public void UpdatePOIs(ICollection<IPOI> pois)
        {
            if (pois == null)
            {
                throw new ArgumentNullException();
            }

            if (pois.Count == 0)
            {
                return;
            }
            foreach (IPOI poi in pois)
            {
                foreach (var product in poi.PRODUCTS)
                {
                    UpdatePOIs(product, poi);
                }
            }
        }

        /* Updates all existing orders in the frontend database. this function does not
         * add new records into the database. it will only change order details.
         * 
         * Parameters:
         *      orders: ICollection<IPOI> - source of order data. List of
         *                                           pois, that will be updated                            
         */
        public void UpdateOrders(ICollection<IOrder> orders)
        {
            if (orders == null)
            {
                throw new ArgumentNullException();
            }

            if (orders.Count == 0)
            {
                return;
            }


            foreach (var ord in orders)
            {
                var order_query = from o in _frontendModel.ORDERS
                                  where o.order_id == ord.FRONTEND_ID
                                  select o;
                if (order_query.Count() == 0)
                {
                    return;
                }

                var order = order_query.First();

                var status_query = from s in _frontendModel.STATUSES
                                   where s.status_id == ord.STATUS
                                   select s.status_code;

                if (status_query.Count() > 0)
                {
                    order.order_status = status_query.First();
                }
            }
            _frontendModel.SaveChanges();
        }

        /* Updates all Reviews in the frontend database. Method does not add new record into
         * database.
         * 
         * Parameters:
         *      reviews: ICollection<IReview> - source of Review data. List of
         *                                           reviews, that will be updated                            
         */
        public void UpdateReview(ICollection<IReview> reviews)
        {
            if (reviews == null)
            {
                throw new ArgumentNullException();
            }

            if (reviews.Count == 0)
            {
                return;
            }

            foreach (var review in reviews)
            {
                var rev = from r in _frontendModel.REVIEWS
                          where r.review_id == review.ID
                          select r;
                if (rev.Count() == 0)
                {
                    continue;
                }

                var review_data = rev.First();
            }
        }

        public void UpdateCustomers(ICollection<ICustomer> customers)
        {
            if (customers == null)
            {
                throw new ArgumentNullException();
            }

            if (customers.Count == 0)
            {
                return;
            }

            foreach (var cust in customers)
            {
                if (cust.FRONTEND_ID == null || cust.FRONTEND_ID == 0)
                {
                    continue;
                }

                var cust_query = from c in _frontendModel.CUSTOMERS
                                 where c.id == cust.FRONTEND_ID
                                 select c;

                if (cust_query.Count() == 0)
                {
                    continue;
                }

                var customer = cust_query.First();

                if (customer.CUSTOMER_DETAILS.Count == 0)
                {
                    continue;
                }

                var details = customer.CUSTOMER_DETAILS.First();

                details.f_name = cust.FIRSTNAME;
                details.l_name = cust.LASTNAME;
                details.street = cust.STREET;
                details.state = cust.STATE;
                details.email = cust.EMAIL;
                details.zip = cust.ZIP;
                details.phone = cust.PHONE;
                details.city = cust.CITY;
            }
            _frontendModel.SaveChanges();
        }
        #endregion UPDATE

        #region GET_DATA

        /* Returns all Reviews that were added after time specified in the parameter lastSync
         * if no new reviews were added, returns empty collection
         */
        public ICollection<IReview> GetReviews(DateTime lastSync)
        {
            var review_query = from r in _frontendModel.REVIEWS
                               where r.time > lastSync && r.PRODUCT!=null
                               select r;
            ICollection<IReview> output = new HashSet<IReview>();
            foreach (var review in review_query)
            {
                output.Add(new FEReview(review));
            }

            return output;
        }

        /*Retruns collection of all orders that were added after time specified in the parameter
         * last sync. if no orders were added, returns empty collection
         */
        public ICollection<IOrder> GetOrders(DateTime lastSync)
        {
            var order_query = from o in _frontendModel.ORDERS
                              where o.order_date > lastSync
                              select o;
            HashSet<IOrder> orders = new HashSet<IOrder>();
            foreach (var order in order_query)
            {
                orders.Add(new FEOrder(order));
            }
            return orders;
        }

        public ICollection<IProduct> GetProducts(DateTime lastSync)
        {
            var prod_query = from p in _frontendModel.PRODUCTS
                             select p;

            HashSet<IProduct> products = new HashSet<IProduct>();

            foreach (var prod in prod_query)
            {
                products.Add(new FEProduct(prod));
            }

            return products;
        }

        public ICollection<ICustomer> GetCustomers(DateTime lastSync)
        {
            var cust_query = from c in _frontendModel.CUSTOMERS
                             where c.lastvisitDate > lastSync
                             select c;

            HashSet<ICustomer> customers = new HashSet<ICustomer>();
            foreach (var cust in cust_query)
            {
                customers.Add(new FECustomer(cust));
            }

            return customers;
        }
        #endregion GET_DATA

        /* PRIVATE METHODS */

        private FrontendEntities _frontendModel = new FrontendEntities(); //reference to entity model of frontend db.

        /* adds info about db changes into provided array */
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

        /* updates joomlas main menu */
        private void UpdateMainMenu(ICollection<ICategory> categories)
        {
            //Get menu root element
            var root = (from m in _frontendModel.MENU
                        where m.id == 1
                        select m).Single();
            int lastLeftNode = root.rgt;

            //update each category in main menu
            foreach (ICategory cat in categories)
            {
                /* Finds category in main menu */
                var menu_item_query = from m in _frontendModel.MENU
                                      where m.menutype == "ourtours"
                                      select m;
                bool contains = false;
                MENU menu_item = null;
                foreach (var m in menu_item_query)
                {
                    /* Category that is added into database using this method is idetified
                     * by column note, which consists of text cat_[cat_id]
                     */
                    if (m.note == "cat_" + cat.ID)
                    {
                        contains = true;
                        menu_item = m;
                        break;
                    }
                }
                //if menu item was not found create new item
                if (!contains)
                {
                    //generate alias for category name
                    string alias = cat.NAME.Replace(" ", "-").ToLower();

                    //new item element
                    MENU new_item = new MENU
                    {
                        menutype = "ourtours",
                        title = cat.NAME,
                        alias = alias,
                        note = "cat_" + cat.ID,
                        path = alias,
                        link = String.Format("index.php?option=com_jshopping&controller=category&task=view&category_id={0}&manufacturer_id=&label_id=&vendor_id=&page=&price_from=&price_to=", cat.FRONTEND_ID),
                        type = "component",
                        published = cat.PUBLISHED ? (sbyte)1 : (sbyte)0,
                        parent_id = 1,
                        level = 1,
                        component_id = 10000,
                        checked_out = 0,
                        checked_out_date = DateTime.MinValue,
                        browserNav = 0,
                        access = 1,
                        img = "",
                        template_style_id = 0,
                        param = "{\"menu-anchor_title\":\"\",\"menu-anchor_css\":\"\",\"menu_image\":\"\",\"menu_text\":1,\"page_title\":\"\",\"show_page_heading\":0,\"page_heading\":\"\",\"pageclass_sfx\":\"\",\"menu-meta_description\":\"\",\"menu-meta_keywords\":\"\",\"robots\":\"\",\"secure\":0}",
                        lft = lastLeftNode,
                        rgt = lastLeftNode + 1,
                        home = 0,
                        language = "*",
                        client_id = 0
                    };

                    //calculate new tree reference of main menu element
                    lastLeftNode += 2;
                    _frontendModel.MENU.Add(new_item);
                }
                else
                {
                    //update name and published status
                    menu_item.title = cat.NAME;
                    menu_item.published = cat.PUBLISHED ? (sbyte)1 : (sbyte)0;
                }
            }
            //update new tree reference in main menu element
            root.rgt = lastLeftNode;
            _frontendModel.SaveChanges();
        }

        /* updates product list in the category */
        private void RefreshProductList(ICategory cat, CATEGORIES category)
        {
            //removes all products from category
            category.PRODUCT_TO_CATEGORY.Clear();

            //order of product in category
            int maxOrder = 1;

            foreach (var c in cat.PRODUCTS)
            {
                //skip if product was not added into the frontend database yet.
                if (!c.FRONTEND_ID.HasValue) continue;

                //add product to category
                category.PRODUCT_TO_CATEGORY.Add(
                    new PRODUCT_TO_CATEGORY
                    {
                        product_id = c.FRONTEND_ID.Value,
                        product_ordering = maxOrder++
                    });
            }
            _frontendModel.SaveChanges();
        }
        
        /* edits existing tour in the frontend database */
        private void EditTour(IProduct tour)
        {
            if (tour.FRONTEND_ID == null || tour.FRONTEND_ID == 0)
            {
                throw new ArgumentException("Tour is not registered in the frontend");
            }
            Debug.WriteLine("Edit tour: " + tour.ID + ", Name: " + tour.NAME);
            //get product record from database
            PRODUCTS dst_tour = (from c in _frontendModel.PRODUCTS
                                 where c.product_id == tour.FRONTEND_ID
                                 select c).Single();

            dst_tour.description_de_DE = dst_tour.description_en_GB = GetDescription(tour);
            dst_tour.product_ean = "";
            dst_tour.min_price = getValue(tour.MIN_PRICE);
            dst_tour.name_de_DE = dst_tour.name_en_GB = tour.NAME;
            dst_tour.product_price = getValue(tour.PRICE);
            dst_tour.product_publish = tour.DELETED ? false : tour.PUBLISHED;
            dst_tour.product_quantity = getValue(tour.QUANTITY);
            dst_tour.short_description_de_DE = dst_tour.short_description_en_GB = tour.SHORT_DESCRIPTION;
            dst_tour.image = tour.MEDIA == null ? "" : tour.MEDIA.PATH;

            _frontendModel.SaveChanges();

            //remove product from all categories
            dst_tour.PRODUCT_CATEGORY.Clear();

            
            
            foreach (var cat in tour.CATEGORIES)
            {
                //add product to  category
                dst_tour.PRODUCT_CATEGORY.Add(
                    new PRODUCT_TO_CATEGORY { category_id = cat.FRONTEND_ID.Value, product_ordering = 0 });

                _frontendModel.SaveChanges();

                //refresh ordering in the category
                var cats = from c in _frontendModel.PRODUCT_TO_CATEGORY
                           where c.category_id == cat.FRONTEND_ID.Value
                           select c;

                int ordering = 1;
                foreach (var tmp in cats)
                {
                    tmp.product_ordering = ordering++;
                }

                _frontendModel.SaveChanges();   
            }
            //updateMediaReference(tour);
        }

        public void Reset()
        {
            _frontendModel.SaveChanges();
            _frontendModel.Dispose();
            _frontendModel = new FrontendEntities();
        }
        /* adds new tour into frontend database */
        private void AddTour(IProduct tour, Action<int, int> addRecordInformer)
        {
            //new product record
            PRODUCTS dst_tour = new PRODUCTS();
            dst_tour.parent_id = 0;
            dst_tour.product_ean = "";
            dst_tour.product_quantity = getValue(tour.QUANTITY);
            dst_tour.unlimited = false;
            dst_tour.product_date_added = getValue(tour.CREATED_AT, DateTime.Now);
            dst_tour.date_modify = getValue(tour.UPDATED_AT, DateTime.Now);
            dst_tour.product_publish = tour.DELETED ? false : tour.PUBLISHED;
            dst_tour.product_tax_id = 0;
            dst_tour.currency_id = 1;
            dst_tour.product_template = "default";
            dst_tour.product_url = "";
            dst_tour.product_old_price = 0;
            dst_tour.product_buy_price = 0;
            dst_tour.product_price = getValue(tour.PRICE);
            dst_tour.min_price = getValue(tour.MIN_PRICE);
            dst_tour.different_prices = true;
            dst_tour.product_weight = 0;
            dst_tour.image = tour.MEDIA == null ? "" : tour.MEDIA.PATH;
            dst_tour.product_manufacturer_id = 0;
            dst_tour.product_is_add_price = false;
            dst_tour.add_price_unit_id = 3;
            dst_tour.average_rating = 0;
            dst_tour.reviews_count = 0;
            dst_tour.delivery_times_id = 0;
            dst_tour.hits = 0;
            dst_tour.weight_volume_units = 0;
            dst_tour.basic_price_unit_id = 0;
            dst_tour.label_id = 0;
            dst_tour.vendor_id = 0;
            dst_tour.access = 1;
            dst_tour.name_de_DE = dst_tour.name_en_GB = tour.NAME;
            dst_tour.meta_title_de_DE = dst_tour.meta_title_en_GB = "";
            dst_tour.alias_de_DE = dst_tour.alias_en_GB = "";
            dst_tour.short_description_de_DE = dst_tour.short_description_en_GB = tour.SHORT_DESCRIPTION;
            dst_tour.meta_description_de_DE = dst_tour.meta_description_en_GB = "";
            dst_tour.description_de_DE = dst_tour.description_en_GB = GetDescription(tour);
            dst_tour.meta_keyword_de_DE = dst_tour.meta_keyword_en_GB = "";

            //add product to categories
            int i = 0;
            foreach (var cat in tour.CATEGORIES)
            {
                dst_tour.PRODUCT_CATEGORY.Add(new PRODUCT_TO_CATEGORY { category_id = getValue(cat.FRONTEND_ID), product_ordering = i });
                i++;
            }

            _frontendModel.PRODUCTS.Add(dst_tour);

            _frontendModel.SaveChanges();
            //inform application about added record
            addRecordInformer(tour.ID, dst_tour.product_id);
        }

        /* generates description of product */
        private string GetDescription(IProduct tour)
        {
            string output = "<div id='tour_description'>";
            output += tour.DESCRIPTION + "<br><br>";
            output += "<strong>Points of interest:</strong>";
            output += "<ul id='poi_list'>";

            //add poi information to description
            foreach (var poi in tour.POIS)
            {
                output += "<li id='poi_" + poi.ID + "'>" + poi.NAME + "</li>";
            }
            output += "</ul>";
            output += "</div>";

            return output;
        }
       
        /* updates media records in frontend database for provided medium */
        private void updateMediaReference(IMedia m)
        {   
            //get media references
            var med = from i in _frontendModel.IMAGES
                      where i.image_name == m.PATH
                      select i;
            //remove all media reference from database
            foreach (var image in med)
            {
                _frontendModel.IMAGES.Remove(image);
            }

            if (m.POIS == null) return;
            if (m.POIS.PRODUCTS == null) return;
            //gets product reference
            var products = from prod in m.POIS.PRODUCTS
                           select prod.FRONTEND_ID;

            //adds media reference to all related products
            foreach (var prod in products)
            {
                var max_query = from image in _frontendModel.IMAGES
                                where image.product_id == prod
                                select image.ordering;
                int max;
                if (max_query.Count() == 0)
                {
                    max = 0;
                }
                else
                {
                    max = max_query.Max();
                }
                IMAGES img = new IMAGES
                {
                    image_name = m.PATH,
                    name = m.NAME,
                    ordering = (sbyte)(max + 1)
                };
                var product = (from p in _frontendModel.PRODUCTS
                               where p.product_id == prod
                               select p).Single();
                product.IMAGES.Add(img);
            }
            _frontendModel.SaveChanges();
        }
        private void updateMediaReference(IProduct p)
        {
            var prod_query = from prod in _frontendModel.PRODUCTS
                             where prod.product_id == p.FRONTEND_ID
                             select prod;
            if (prod_query.Count() == 0)
            {
                return;
            }

            PRODUCTS product = prod_query.First();

            product.IMAGES.Clear();
            sbyte i = 0;
            foreach (IPOI poi in p.POIS)
            {
                foreach (IMedia media in poi.MEDIA)
                {
                    if (!media.PUBLISHED || media.MEDIUMTYPE != "IMG") continue;

                    product.IMAGES.Add(new IMAGES 
                    {
                        image_name = media.PATH,
                        name=media.NAME,
                        ordering = i
                    });
                    i++;
                }
            }
            _frontendModel.SaveChanges();
        }
        /* updates point of interest information in product description */
        private void UpdatePOIs(IProduct product, IPOI poi)
        {
            var prod = from p in _frontendModel.PRODUCTS
                       where p.product_id == product.FRONTEND_ID
                       select p;
            if (prod.Count() == 0)
                return;
            var tour = prod.Single();

            string description = tour.description_de_DE;
            HtmlDocument doc = new HtmlDocument();
            doc.LoadHtml(description);
            HtmlNode poiNode = doc.GetElementbyId("poi_" + poi.ID);
            //if poi is not in description adds new poi in the document
            if (poiNode == null)
            {
                poiNode = HtmlNode.CreateNode("<li id='poi_" + poi.ID + "'>" + poi.NAME + "</li>");
                doc.GetElementbyId("poi_list").ChildNodes.Add(poiNode);
            }
            else
            {
                poiNode.InnerHtml = poi.NAME;
            }
            tour.description_de_DE = tour.description_en_GB = doc.DocumentNode.OuterHtml;
        }

        /* converts nullable type to not nullabe. if parameter is null sets it to default value */
        private T getValue<T>(Nullable<T> number, T defValue = default(T)) where T : struct
        {
            return number.HasValue ? number.Value : defValue;
        }
    }
}

