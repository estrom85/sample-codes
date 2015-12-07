using DataProvider.Interfaces;
using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using DataProvider.EntityModels.BackendModel;

namespace DataProvider.DataModel.BackendModel
{
    public class BEVariant : IVariant
    {
        private PRODUCTVARIANTS _variant;

        public BEVariant(PRODUCTVARIANTS variant)
        {
            _variant = variant;
        }

        #region COMPARE_LOGIC_DEFINITION
        public override bool Equals(object obj)
        {
            BEVariant other = obj as BEVariant;
            if (other != null)
            {
                return _variant.Equals(other._variant);
            }
            return false;
        }

        public override int GetHashCode()
        {
            return _variant.GetHashCode();
        }
        #endregion COMPARE_LOGIC_DEFINITION

        public int ID
        {
            get { return _variant.ID; }
        }

        public string SYSTEM_UUID
        {
            get { return _variant.SYSTEM_UUID; }
        }

        public int? FRONTEND_ID
        {
            get { return _variant.FRONTEND_ID; }
        }

        public int PRODUCT_ID
        {
            get { return _variant.PRODUCT_ID; }
        }

        public decimal? PRICE
        {
            get { return (decimal?)_variant.PRICE; }
        }

        public int? QUANTITY
        {
            get { return _variant.QUANTITY; }
        }

        public DateTime? CREATED_AT
        {
            get { return _variant.CREATED_AT; }
        }

        public DateTime? UPDATED_AT
        {
            get { return _variant.CREATED_AT; }
        }

        public bool PUBLISHED
        {
            get { return _variant.PUBLISHED.HasValue ? _variant.PUBLISHED.Value != 0 : false; }
        }

        public bool DELETED
        {
            get { return _variant.DELETED_AT.HasValue; }
        }

        private IProduct _product = null;

        public IProduct PRODUCT
        {
            get
            {
                if (_product == null)
                {
                    _product = new BEProduct(_variant.PRODUCTS);
                }
                return _product;
            }
        }

        private ICollection<IVariantValue> _values = null;

        private IVariantValue _date;
        public IVariantValue DATE
        {
            get 
            {
                if (_date == null)
                {
                    _date = new BEVariantValue(_variant.DATE);
                }
                return _date;
            }
        }

        private IVariantValue _package;
        public IVariantValue PACKAGE
        {
            get 
            {
                if (_package == null)
                {
                    _package = new BEVariantValue(_variant.PACKAGE);
                }
                return _package;
            }
        }
    }
}