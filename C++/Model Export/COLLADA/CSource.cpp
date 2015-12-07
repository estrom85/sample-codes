#include "CSource.h"
#include <iostream>
#include <sstream>
/******************************Param******************************/
void CParam::load(pugi::xml_node &param)
{
    mName=param.attribute("name").value();
    mType=param.attribute("type").value();
}

/****************************Accessor*****************************/
void CAccessor::load(pugi::xml_node &accessor)
{
    mSource=accessor.attribute("source").value();
    mCount=accessor.attribute("count").as_uint();
    mStride=accessor.attribute("stride").as_uint();

    for(pugi::xml_node param=accessor.child("param");param;param=param.next_sibling("param"))
    {
        CParam parameter;
        parameter.load(param);
        mParams.push_back(parameter);
    }

}

/**************************Techniquie common**********************/
void CTechniqueCommon::load(pugi::xml_node &technique_common)
{
    pugi::xml_node accessor=technique_common.child("accessor");
    mAccessor.load(accessor);

}


/***************************Name_array****************************/
CNameArray::CNameArray(const CNameArray &other)
{
    mCount=other.mCount;
    mID=other.mID;

    mNames=new string[mCount];

    for(int i=0;i<mCount;i++)
    {
        mNames[i]=other.mNames[i];
    }
}

CNameArray::~CNameArray()
{
    delete [] mNames;
}

const CNameArray& CNameArray::operator=(const CNameArray &other)
{
    if(this==&other) return *this;

    delete [] mNames;

    mCount=other.mCount;
    mID=other.mID;

    mNames=new string[mCount];

    for(int i=0;i<mCount;i++)
    {
        mNames[i]=other.mNames[i];
    }

    return *this;

}

void CNameArray::load(pugi::xml_node &name_array)
{
    if(!name_array) return;

    mCount=name_array.attribute("count").as_uint();
    mID=name_array.attribute("id").value();
    mNames=new string[mCount];

    std::stringstream ss;
    ss<<name_array.child_value();
    string temp;
    int i=0;
    while(ss>>temp)
    {
        mNames[i]=temp;
        i++;
    }
}

/***************************Float_array***************************/
CFloatArray::CFloatArray(const CFloatArray &other)
{
    mCount=other.mCount;
    mID=other.mID;

    mFloats=new float[mCount];

    for(int i=0;i<mCount;i++)
    {
        mFloats[i]=other.mFloats[i];
    }
}

CFloatArray::~CFloatArray()
{
    delete [] mFloats;
}

const CFloatArray& CFloatArray::operator=(const CFloatArray &other)
{
    if (this==&other) return *this;

    delete[] mFloats;

    mCount=other.mCount;
    mID=other.mID;
    for(int i=0;i<mCount;i++)
    {
        mFloats[i]=other.mFloats[i];
    }

    return *this;
}

void CFloatArray::load(pugi::xml_node &float_array)
{
    if(!float_array) return;

    mCount=float_array.attribute("count").as_uint();
    mID=float_array.attribute("id").value();
    mFloats=new float [mCount];

    std::stringstream ss;
    ss<<float_array.child_value();

    int i=0;
    float temp;
    while (ss>>temp)
    {
        mFloats[i]=temp;
        i++;
    }
}

/*****************************Source******************************/
void CSource::load(pugi::xml_node &source)
{
   mID=source.attribute("id").value();

   pugi::xml_node floatArray=source.child("float_array");
   mFloatArray.load(floatArray);

   pugi::xml_node nameArray=source.child("Name_array");
   mNameArray.load(nameArray);

   pugi::xml_node techCommon=source.child("technique_common");
   mTechniqueCommon.load(techCommon);



}
