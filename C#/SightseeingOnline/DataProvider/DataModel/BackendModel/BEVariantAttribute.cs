using DataProvider.Interfaces;
using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using DataProvider.EntityModels.BackendModel;

namespace DataProvider.DataModel.BackendModel
{
    class BEVariantAttribute : IVariantAttribute
    {
        PRODUCTVARIANTATTRIBUTES _attribute;

        public BEVariantAttribute(PRODUCTVARIANTATTRIBUTES attribute)
        {
            _attribute = attribute;
        }

        #region COMPARE_LOGIC_DEFINITION
        public override bool Equals(object obj)
        {
            BEVariantAttribute other = obj as BEVariantAttribute;
            if(other != null)
            {
                return _attribute.Equals(other._attribute);
            }
            return false;
        }

        public override int GetHashCode()
        {
            return _attribute.GetHashCode();
        }
        #endregion COMPARE_LOGIC_DEFINITION

        public int ID
        {
            get { return _attribute.ID; }
        }

        public string SYSTEM_UUID
        {
            get { return _attribute.SYSTEM_UUID; }
        }

        public int? FRONTEND_ID
        {
            get { return _attribute.FRONTEND_ID; }
        }

        public string NAME
        {
            get { return _attribute.NAME; }
        }

        public string DESCRIPTION
        {
            get { return _attribute.DESCRIPTION; }
        }

        public DateTime? CREATED_AT
        {
            get { return _attribute.CREATED_AT; }
        }

        public DateTime? UPDATED_AT
        {
            get { return _attribute.UPDATED_AT; }
        }

        private ICollection<IVariantValue> _values = null;

        public ICollection<IVariantValue> PRODUCTVARIANTATTRIBUTEVALUES
        {
            get 
            {
                if (_values == null)
                {
                    _values = new HashSet<IVariantValue>();
                    foreach (PRODUCTVARIANTATTRIBUTEVALUES val in _attribute.PRODUCTVARIANTATTRIBUTEVALUES)
                    {
                        _values.Add(new BEVariantValue(val));
                    }
                }
                return _values;
            }
        }
    }
}
