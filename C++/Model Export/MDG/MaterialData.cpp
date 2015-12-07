#include "CMDG.h"

/**********************Texture Data class**************************/

CTextureData::CTextureData()
{
    textureDataSize=sizeof(filename);

}

CTextureData::CTextureData(const CTextureData &other)
{
    textureDataSize=other.textureDataSize;
    strcpy(filename,other.filename);

}

const CTextureData &CTextureData::operator =(const CTextureData &other)
{
    if (this==&other) return *this;

    textureDataSize=other.textureDataSize;
    strcpy(filename,other.filename);

    return *this;
}

void CTextureData::loadTextureData(CCollada *collada)
{
    if(collada->mImageLibrary.mImages.size()!=0)
    {
    CImageFile img=collada->mImageLibrary.mImages[0];

    strcpy(filename,img.mInitFrom.mFilename.c_str());
    }
    else
        filename[0]='\0';

}

void CTextureData::saveTextureData(std::ofstream &file, unsigned int offset)
{
    file.seekp(offset,std::ios_base::beg);

    file.write(filename,sizeof(filename));


}

void CTextureData::loadTextureData(std::ifstream &file, unsigned int offset)
{
    file.seekg(offset,std::ios_base::beg);

    file.read(filename,sizeof(filename));

}

const char* CTextureData::getTexFilename()
{
    return filename;
}

unsigned int CTextureData::size()
{
    return textureDataSize;

}
/*****************************Materials class*************************/
CMaterials::CMaterials()
{
    id[0]='\0';
    for (int i=0;i<4;i++)
    {
        float value=0;
        if (i==3) value=1;
        emmision[i]=value;
        ambient[i]=value;
        specular[i]=value;
    }

    shininess=0;
    flags=0;
}

CMaterials::CMaterials(const CMaterials &other)
{
    strcpy(id,other.id);
    flags=other.flags;

    for (int i=0;i<4;i++)
    {
        specular[i]=other.specular[i];
        ambient[i]=other.ambient[i];
        emmision[i]=other.emmision[i];

    }
    shininess=other.shininess;

}

const CMaterials &CMaterials::operator =(const CMaterials &other)
{
    if (this==&other) return *this;

    strcpy(id,other.id);
    flags=other.flags;

    for (int i=0;i<4;i++)
    {
        specular[i]=other.specular[i];
        ambient[i]=other.ambient[i];
        emmision[i]=other.emmision[i];

    }
    shininess=other.shininess;
    return *this;
}


/**************************Material Data class************************/

CMaterialData::CMaterialData()
{
    numOfMaterials=0;
    materialDataSize=sizeof(numOfMaterials);

}

void CMaterialData::loadMaterialData(CCollada *collada)
{
    numOfMaterials=collada->mMaterialLibrary.mMaterials.size();

    for (int i=0;i<collada->mMaterialLibrary.mMaterials.size();i++)
    {
        CMaterial *materialData=&collada->mMaterialLibrary.mMaterials[i];
        CMaterials material;

        strcpy(material.id,materialData->mID.c_str());



        CEffect *effect;

        for(int j=0;j<collada->mEffectLibrary.mEffects.size();j++)
        {
            effect=&collada->mEffectLibrary.mEffects[j];

            if (materialData->mEfect.mURL==std::string("#")+effect->mID)
                break;

                effect=0;
        }

        if (effect)
        {

            if(effect->mAmbient.mColor[0]>-1)
                material.flags=material.flags|AMBIENT;
            if(effect->mEmmision.mColor[0]>-1)
                material.flags=material.flags|EMMISION;
            if(effect->mSpecular.mColor[0]>-1)
                material.flags=material.flags|SPECULAR;
            if(effect->mShininess.mValue>-1)
                material.flags=material.flags|SHININESS;

            for(int i=0;i<4;i++)
            {
                if (material.flags&AMBIENT)
                    material.ambient[i]=effect->mAmbient.mColor[i];
                if (material.flags&EMMISION)
                    material.emmision[i]=effect->mEmmision.mColor[i];
                if (material.flags&SPECULAR)
                    material.specular[i]=effect->mSpecular.mColor[i];
            }

            if(material.flags&SHININESS)
                material.shininess=effect->mShininess.mValue;
        }

        this->materialData.push_back(material);
    }

    materialDataSize=sizeof(numOfMaterials)+numOfMaterials*sizeof(CMaterials);
}

void CMaterialData::saveMaterialData(std::ofstream &file, unsigned int offset)
{
    file.seekp(offset,std::ios_base::beg);

    file.write((char*)&numOfMaterials,sizeof(numOfMaterials));

    for (int i=0;i<numOfMaterials;i++)
    {
        CMaterials *material = &materialData[i];
        file.write(material->id,sizeof(material->id));
        file.write((char*)&material->flags,sizeof(material->flags));

        if(material->flags&EMMISION)
            for(int i=0;i<4;i++)
                file.write((char*)&material->emmision[i],sizeof(material->emmision[i]));

        if(material->flags&AMBIENT)
            for (int i=0;i<4;i++)
                file.write((char*)&material->ambient[i],sizeof(material->ambient[i]));

        if(material->flags&SPECULAR)
            for (int i=0;i<4;i++)
                file.write((char*)&material->specular[i],sizeof(material->specular[i]));

        if(material->flags&SHININESS)
            file.write((char*)&material->shininess,sizeof(material->shininess));

    }


}

void CMaterialData::loadMaterialData(std::ifstream &file, unsigned int offset)
{
    file.seekg(offset,std::ios_base::beg);

    file.read((char*)&numOfMaterials,sizeof(numOfMaterials));

    for(int i=0;i<numOfMaterials;i++)
    {
        CMaterials material;
        file.read(material.id,sizeof(material.id));
        file.read((char*)&material.flags,sizeof(material.flags));

        if(material.flags&EMMISION)
            for(int i=0;i<4;i++)
                file.read((char*)&material.emmision[i],sizeof(material.emmision[i]));

        if(material.flags&AMBIENT)
            for(int i=0;i<4;i++)
                file.read((char*)&material.ambient[i],sizeof(material.ambient[i]));

        if(material.flags&SPECULAR)
            for(int i=0;i<4;i++)
                file.read((char*)&material.specular[i],sizeof(material.specular[i]));

        if(material.flags&SHININESS)
            file.read((char*)&material.shininess,sizeof(material.shininess));

        materialData.push_back(material);
    }

    materialDataSize=sizeof(numOfMaterials)+numOfMaterials*sizeof(CMaterials);

}

unsigned int CMaterialData::size()
{
    return materialDataSize;
}

CMaterials* CMaterialData::getMaterial(char *id)
{
    CMaterials* result;

    for (int i=0;i<numOfMaterials;i++)
    {
        char* temp="#";

        result=&materialData[i];

        strcat(temp,result->id);

        if(strcmp(temp,id)==0)
            break;

        result=0;
    }

    return result;

}
