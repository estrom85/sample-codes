using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using DataProvider.EntityModels.BackendModel;
using DataProvider.Interfaces;

namespace DataProvider.DataModel.BackendModel
{
    public class BECategory : ICategory
    {
        private PRODUCTCATEGORIES _category;

        public BECategory(PRODUCTCATEGORIES category)
        {
            _category = category;
        }

        #region COMPARE_LOGIC_DEFINITION

        public override bool Equals(object obj)
        {
            BECategory other = obj as BECategory;
            if (other!=null)
            {
                return _category.Equals(other._category);
            }
            return false;
        }

        public override int GetHashCode()
        {
            return _category.GetHashCode();
        }

        #endregion COMPARE_LOGIC_DEFINITION

        public int ID
        {
            get { return _category.ID; }
        }

        public string SYSTEM_UUID
        {
            get { return _category.SYSTEM_UUID; }
        }

        public int? FRONTEND_ID
        {
            get { return _category.FRONTEND_ID; }
        }

        public string NAME
        {
            get { return _category.NAME; }
        }

        public DateTime? CREATED_AT
        {
            get { return _category.CREATED_AT; }
        }

        public DateTime? UPDATED_AT
        {
            get { return _category.UPDATED_AT; }
        }

        public bool DELETED
        {
            get 
            { 
                //return _category.DELETED.HasValue ? _category.DELETED.Value != 0 : false; 
                return _category.DELETED_AT.HasValue;
            }
        }

        public bool PUBLISHED
        {
            get { return _category.PUBLISHED.HasValue ? _category.PUBLISHED.Value != 0 : false; }
        }

        public ICollection<IProduct> _products = null;
        public ICollection<IProduct> PRODUCTS
        {
            get 
            {
                if (_products == null)
                {
                    _products = new HashSet<IProduct>();
                    foreach (PRODUCTS prod in _category.PRODUCTS)
                    {
                        _products.Add(new BEProduct(prod));
                    }
                }
                return _products;
            }
        }





        
    }
}
