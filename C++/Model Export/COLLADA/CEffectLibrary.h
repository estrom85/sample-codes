#ifndef _CEFFECTLIBRARY_H_
#define _CEFFECTLIBRARY_H_

#include "../pugixml.hpp"
#include <vector>
#include <string>
using std::vector;
using std::string;

class CEmmision
{
public:
    float                   mColor[4];

public:
    CEmmision(){mColor[0]=mColor[1]=mColor[2]=mColor[3]=-1;}


};

class CAmbient
{
public:
    float                   mColor[4];

public:
    CAmbient(){mColor[0]=mColor[1]=mColor[2]=mColor[3]=-1;}

};

class CSpecular
{
public:
    float                   mColor[4];

public:
    CSpecular() {mColor[0]=mColor[1]=mColor[2]=mColor[3]=-1;}


};

class CShininess
{
public:
    float                   mValue;

public:
    CShininess(){mValue=-1;}

};

class CTransparency
{
public:
    float                   mValue;

public:
    CTransparency(){mValue=-1;}
};

class CEffect
{
public:
    string                  mID;
    string                  mTexID;
    CEmmision               mEmmision;
    CAmbient                mAmbient;
    CSpecular               mSpecular;
    CShininess              mShininess;
    CTransparency           mTransparency;


};

class CEffectLibrary
{
public:
    vector<CEffect>         mEffects;
public:
    void load(pugi::xml_node& node);
};

#endif // CEFFECTLIBRARY_H
