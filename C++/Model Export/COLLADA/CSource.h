#ifndef _CSOURCE__H_
#define _CSOURCE_H_

#include <string>
#include <vector>
#include "../pugixml.hpp"

using std::string;
using std::vector;

class CParam
{
    public:
    string                      mName;
    string                      mType;

    public:
            void load(pugi::xml_node &param);
};

class CAccessor
{
    public:
    string                      mSource;
    unsigned int                mCount;
    unsigned int                mStride;
    vector<CParam>              mParams;

    public:
        CAccessor(){mCount=0;mStride=0;};
        void load(pugi::xml_node &accessor);

};

class CTechniqueCommon
{
    public:
    CAccessor                   mAccessor;

    public:
        void load(pugi::xml_node &technique_common);


};

class CFloatArray
{
    public:
    unsigned int                mCount;
    string                      mID;
    float                       *mFloats;

    public:
        CFloatArray(){mFloats=0; mCount=0;};                                  //implicitny konstruktor
        CFloatArray(const CFloatArray &other);                      //kopirovaci konstruktor
        ~CFloatArray();                                             //destruktor

        const CFloatArray& operator=(const CFloatArray &other);     //pretazeny operator priradenia

        void load(pugi::xml_node &float_array);

};

class CNameArray
{
    public:
    unsigned int                mCount;
    string                      mID;
    string                      *mNames;

    public:
        CNameArray(){mNames=0; mCount=0;};
        CNameArray(const CNameArray &other);
        ~CNameArray();

        const CNameArray& operator=(const CNameArray &other);

        void load(pugi::xml_node &name_array);

};

class CSource
{
    public:
    string                      mID;
    CFloatArray                 mFloatArray;
    CNameArray                  mNameArray;
    CTechniqueCommon            mTechniqueCommon;

    public:
        void load(pugi::xml_node &source);

};


#endif
