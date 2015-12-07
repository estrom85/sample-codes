using DataProvider.Interfaces;
using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using DataProvider.EntityModels.BackendModel;

namespace DataProvider.DataModel.BackendModel
{
    public class BEVariantValue : IVariantValue
    {
        PRODUCTVARIANTATTRIBUTEVALUES _value;
        public BEVariantValue(PRODUCTVARIANTATTRIBUTEVALUES value)
        {
            _value = value;
        }

        #region COMPARE_LOGIC_DEFINITION
        public override bool Equals(object obj)
        {
            BEVariantValue other = obj as BEVariantValue;

            if (other != null)
            {
                return _value.Equals(other._value);
            }
            return false;
        }

        public override int GetHashCode()
        {
            return _value.GetHashCode();
        }
        #endregion COMPARE_LOGIC_DEFINITION

        public int ID
        {
            get { return _value.ID; }
        }

        public string SYSTEM_UUID
        {
            get { return _value.SYSTEM_UUID; }
        }

        public int? FRONTEND_ID
        {
            get { return _value.FRONTEND_ID; }
        }

        public string NAME
        {
            get { return _value.NAME; }
        }

        public int? PRODUCTVARIANTATTRIBUTE_ID
        {
            get { return _value.PRODUCTVARIANTATTRIBUTE_ID; }
        }

        public DateTime? CREATED_AT
        {
            get { return _value.CREATED_AT; }
        }

        public DateTime? UPDATED_AT
        {
            get { return _value.UPDATED_AT; }
        }

        public bool DELETED
        {
            get { return _value.DELETED_AT.HasValue; }
        }

        public bool PUBLISHED
        {
            get { return _value.PUBLISHED.HasValue ? _value.PUBLISHED != 0 : false; }
        }

        private ICollection<IVariant> _variant = null;

        public ICollection<IVariant> VARIANTS
        {
            get 
            {
                if (_variant == null)
                {
                    _variant = new HashSet<IVariant>();
                    foreach (PRODUCTVARIANTS variant in _value.PRODUCTVARIANTS)
                    {
                        _variant.Add(new BEVariant(variant));
                    }
                }
                return _variant;
            }
        }

        public IVariantAttribute _attribute;
        public IVariantAttribute ATTRIBUTE
        {
            get 
            {
                if (_attribute == null)
                {
                    _attribute = new BEVariantAttribute(_value.PRODUCTVARIANTATTRIBUTES);
                }
                return _attribute; 
            }
        }


        
    }
}
